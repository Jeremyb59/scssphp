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

namespace ScssPhp\ScssPhp\SourceSpan;

use League\Uri\Contracts\UriInterface;
use ScssPhp\ScssPhp\Util\Path;

/**
 * A mixin for easily implementing {@see SourceSpan}.
 *
 * This implements the {@see SourceSpan} methods in terms of {@see getStart}, {@see getEnd}, and
 * {@see getText}. This assumes that {@see getStart} and {@see getEnd} have the same source URL, that
 * {@see getStart} comes before {@see getEnd}, and that [text] has a number of characters equal
 * to the distance between {@see getStart} and {@see getEnd}.
 */
abstract class SourceSpanMixin implements SourceSpan
{
    public function getSourceUrl(): ?UriInterface
    {
        return $this->getStart()->getSourceUrl();
    }

    public function getLength(): int
    {
        return $this->getEnd()->getOffset() - $this->getStart()->getOffset();
    }

    public function union(SourceSpan $other): SourceSpan
    {
        if (!Util::isSameUrl($this->getSourceUrl(), $other->getSourceUrl())) {
            throw new \InvalidArgumentException("Source URLs \"{$this->getSourceUrl()}\" and \"{$other->getSourceUrl()}\" don't match.");
        }

        if ($this->getStart()->compareTo($other->getStart()) > 0) {
            $start = $other->getStart();
            $beginSpan = $other;
        } else {
            $start = $this->getStart();
            $beginSpan = $this;
        }
        if ($this->getEnd()->compareTo($other->getEnd()) > 0) {
            $end = $this->getEnd();
            $endSpan = $this;
        } else {
            $end = $other->getEnd();
            $endSpan = $other;
        }

        if ($beginSpan->getEnd()->compareTo($endSpan->getStart()) < 0) {
            throw new \InvalidArgumentException("Spans are disjoint.");
        }

        $text = $beginSpan->getText() . substr($endSpan->getText(), $beginSpan->getEnd()->distance($endSpan->getStart()));

        return new SimpleSourceSpan($start, $end, $text);
    }

    public function compareTo(SourceSpan $other): int
    {
        $result = $this->getStart()->compareTo($other->getStart());

        if ($result !== 0) {
            return $result;
        }

        return $this->getEnd()->compareTo($other->getEnd());
    }

    public function message(string $message): string
    {
        $startLine = $this->getStart()->getLine() + 1;
        $startColumn = $this->getStart()->getColumn() + 1;
        $sourceUrl = $this->getSourceUrl();

        $buffer = "line $startLine, column $startColumn";

        if ($sourceUrl !== null) {
            $prettyUri = Path::prettyUri($sourceUrl);
            $buffer .= " of $prettyUri";
        }

        $buffer .= ": $message";

        $highlight = $this->highlight();
        if ($highlight !== '') {
            $buffer .= "\n";
            $buffer .= $highlight;
        }

        return $buffer;
    }

    public function messageMultiple(string $message, string $label, array $secondarySpans): string
    {
        $startLine = $this->getStart()->getLine() + 1;
        $startColumn = $this->getStart()->getColumn() + 1;
        $sourceUrl = $this->getSourceUrl();

        $buffer = "line $startLine, column $startColumn";

        if ($sourceUrl !== null) {
            $prettyUri = Path::prettyUri($sourceUrl);
            $buffer .= " of $prettyUri";
        }

        $buffer .= ": $message";

        $highlight = $this->highlightMultiple($label, $secondarySpans);
        if ($highlight !== '') {
            $buffer .= "\n";
            $buffer .= $highlight;
        }

        return $buffer;
    }

    public function highlight(): string
    {
        if (!$this instanceof SourceSpanWithContext && $this->getLength() === 0) {
            return '';
        }

        // TODO: Implement highlight() method.
        return '';
    }

    public function highlightMultiple(string $label, array $secondarySpans): string
    {
        // TODO: Implement highlightMultiple() method.
        return '';
    }
}
