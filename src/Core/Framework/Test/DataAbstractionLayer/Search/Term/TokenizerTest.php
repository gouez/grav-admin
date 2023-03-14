<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DataAbstractionLayer\Search\Term;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\DataAbstractionLayer\Search\Term\Tokenizer;

/**
 * @internal
 */
class TokenizerTest extends TestCase
{
    /**
     * @dataProvider cases
     */
    public function testInterpreter(string $term, array $expected): void
    {
        $tokens = (new Tokenizer(2))->tokenize($term);
        static::assertSame($expected, $tokens);
    }

    public static function cases(): array
    {
        return [
            [
                '    ',
                [],
            ],
            [
                'laser AG',
                ['laser', 'ag'],
            ],
            [
                'test a thing',
                ['test', 'thing'],
            ],
            [
                'Österreicher Essen',
                ['österreicher', 'essen'],
            ],
            [
                '!Example"§$%``=)(/&%%$§""!',
                ['example'],
            ],
            [
                'Synergistic Copper DM-10000 FaceMaster',
                ['synergistic', 'copper', 'dm-10000', 'facemaster'],
            ],
        ];
    }
}
