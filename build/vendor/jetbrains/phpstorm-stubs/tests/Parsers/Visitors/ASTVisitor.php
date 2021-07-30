<?php

declare (strict_types=1);
namespace SMTP2GOWPPlugin\StubTests\Parsers\Visitors;

use Exception;
use SMTP2GOWPPlugin\PhpParser\Node;
use SMTP2GOWPPlugin\PhpParser\Node\Const_;
use SMTP2GOWPPlugin\PhpParser\Node\Expr\FuncCall;
use SMTP2GOWPPlugin\PhpParser\Node\Stmt\Class_;
use SMTP2GOWPPlugin\PhpParser\Node\Stmt\ClassMethod;
use SMTP2GOWPPlugin\PhpParser\Node\Stmt\Function_;
use SMTP2GOWPPlugin\PhpParser\Node\Stmt\Interface_;
use SMTP2GOWPPlugin\PhpParser\NodeVisitorAbstract;
use SMTP2GOWPPlugin\StubTests\Model\PHPClass;
use SMTP2GOWPPlugin\StubTests\Model\PHPConst;
use SMTP2GOWPPlugin\StubTests\Model\PHPDefineConstant;
use SMTP2GOWPPlugin\StubTests\Model\PHPFunction;
use SMTP2GOWPPlugin\StubTests\Model\PHPInterface;
use SMTP2GOWPPlugin\StubTests\Model\PHPMethod;
use SMTP2GOWPPlugin\StubTests\Model\PHPProperty;
use SMTP2GOWPPlugin\StubTests\Model\StubsContainer;
use SMTP2GOWPPlugin\StubTests\Parsers\Utils;
class ASTVisitor extends NodeVisitorAbstract
{
    protected bool $isStubCore = \false;
    public function __construct(protected StubsContainer $stubs)
    {
    }
    /**
     * @param Node $node
     * @return void
     * @throws Exception
     */
    public function enterNode(Node $node) : void
    {
        if ($node instanceof Function_) {
            $function = (new PHPFunction())->readObjectFromStubNode($node);
            if ($this->isStubCore) {
                $function->stubBelongsToCore = \true;
            }
            $this->stubs->addFunction($function);
        } elseif ($node instanceof Const_) {
            $constant = (new PHPConst())->readObjectFromStubNode($node);
            if ($this->isStubCore) {
                $constant->stubBelongsToCore = \true;
            }
            if ($constant->parentName === null) {
                $this->stubs->addConstant($constant);
            } elseif ($this->stubs->getClass($constant->parentName) !== null) {
                $this->stubs->getClass($constant->parentName)->constants[$constant->name] = $constant;
            } else {
                $this->stubs->getInterface($constant->parentName)->constants[$constant->name] = $constant;
            }
        } elseif ($node instanceof FuncCall) {
            if ($node->name->parts[0] === 'define') {
                $constant = (new PHPDefineConstant())->readObjectFromStubNode($node);
                if ($this->isStubCore) {
                    $constant->stubBelongsToCore = \true;
                }
                $this->stubs->addConstant($constant);
            }
        } elseif ($node instanceof ClassMethod) {
            $method = (new PHPMethod())->readObjectFromStubNode($node);
            if ($this->isStubCore) {
                $method->stubBelongsToCore = \true;
            }
            if ($this->stubs->getClass($method->parentName) !== null) {
                $this->stubs->getClass($method->parentName)->methods[$method->name] = $method;
            } else {
                $this->stubs->getInterface($method->parentName)->methods[$method->name] = $method;
            }
        } elseif ($node instanceof Interface_) {
            $interface = (new PHPInterface())->readObjectFromStubNode($node);
            if ($this->isStubCore) {
                $interface->stubBelongsToCore = \true;
            }
            $this->stubs->addInterface($interface);
        } elseif ($node instanceof Class_) {
            $class = (new PHPClass())->readObjectFromStubNode($node);
            if ($this->isStubCore) {
                $class->stubBelongsToCore = \true;
            }
            $this->stubs->addClass($class);
        } elseif ($node instanceof Node\Stmt\Property) {
            $property = (new PHPProperty())->readObjectFromStubNode($node);
            if ($this->isStubCore) {
                $property->stubBelongsToCore = \true;
            }
            if ($this->stubs->getClass($property->parentName) !== null) {
                $this->stubs->getClass($property->parentName)->properties[$property->name] = $property;
            }
        }
    }
    public function combineParentInterfaces($interface) : array
    {
        $parents = [];
        if (empty($interface->parentInterfaces)) {
            return $parents;
        }
        /**@var string $parentInterface */
        foreach ($interface->parentInterfaces as $parentInterface) {
            $parents[] = $parentInterface;
            if ($this->stubs->getInterface($parentInterface) !== null) {
                foreach ($this->combineParentInterfaces($this->stubs->getInterface($parentInterface)) as $value) {
                    $parents[] = $value;
                }
            }
        }
        return $parents;
    }
    public function combineImplementedInterfaces($class) : array
    {
        $interfaces = [];
        /**@var string $interface */
        foreach ($class->interfaces as $interface) {
            $interfaces[] = $interface;
            if ($this->stubs->getInterface($interface) !== null) {
                $interfaces[] = $this->stubs->getInterface($interface)->parentInterfaces;
            }
        }
        if ($class->parentClass === null) {
            return $interfaces;
        }
        if ($this->stubs->getClass($class->parentClass) !== null) {
            $inherited = $this->combineImplementedInterfaces($this->stubs->getClass($class->parentClass));
            $interfaces[] = Utils::flattenArray($inherited, \false);
        }
        return $interfaces;
    }
}
