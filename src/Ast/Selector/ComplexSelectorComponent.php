<?php

/**
 * SCSSPHP
 *
 * @copyright 2012-2020 Leaf Corcoran
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link http://scssphp.github.io/scssphp
 */

namespace ScssPhp\ScssPhp\Ast\Selector;

use ScssPhp\ScssPhp\Ast\Css\CssValue;
use ScssPhp\ScssPhp\SourceSpan\FileSpan;
use ScssPhp\ScssPhp\Util\Equatable;
use ScssPhp\ScssPhp\Util\EquatableUtil;

/**
 * A component of a {@see ComplexSelector}.
 *
 * This a {@see CompoundSelector} with one or more trailing {@see Combinator}s.
 *
 * @internal
 */
final class ComplexSelectorComponent implements Equatable
{
    /**
     * This component's compound selector.
     *
     * @var CompoundSelector
     * @readonly
     */
    private $selector;

    /**
     * This selector's combinators.
     *
     * If this is empty, that indicates that it has an implicit descendent
     * combinator. If it's more than one element, that means it's invalid CSS;
     * however, we still support this for backwards-compatibility purposes.
     *
     * @var list<CssValue<string>>
     * @phpstan-var list<CssValue<Combinator::*>>
     * @readonly
     */
    private $combinators;

    /**
     * @var FileSpan
     * @readonly
     */
    private $span;

    /**
     * @param list<CssValue<string>> $combinators
     *
     * @phpstan-param list<CssValue<Combinator::*>> $combinators
     */
    public function __construct(CompoundSelector $selector, array $combinators, FileSpan $span)
    {
        $this->selector = $selector;
        $this->combinators = $combinators;
        $this->span = $span;
    }

    public function getSelector(): CompoundSelector
    {
        return $this->selector;
    }

    public function getSpan(): FileSpan
    {
        return $this->span;
    }

    /**
     * @return list<CssValue<string>>
     * @phpstan-return list<CssValue<Combinator::*>>
     */
    public function getCombinators(): array
    {
        return $this->combinators;
    }

    public function equals(object $other): bool
    {
        return $other instanceof ComplexSelectorComponent && $this->selector->equals($other->selector) && EquatableUtil::listEquals($this->combinators, $other->combinators);
    }

    /**
     * Returns a copy of $this with $combinators added to the end of
     * `$this->combinators`.
     *
     * @param list<CssValue<string>> $combinators
     *
     * @phpstan-param list<CssValue<Combinator::*>> $combinators
     */
    public function withAdditionalCombinators(array $combinators): ComplexSelectorComponent
    {
        if ($combinators === []) {
            return $this;
        }

        return new ComplexSelectorComponent($this->selector, array_merge($this->combinators, $combinators), $this->span);
    }
}
