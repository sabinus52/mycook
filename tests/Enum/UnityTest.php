<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Enum;

use App\Enum\Unity;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class UnityTest extends TestCase
{
    /**
     * Test de l'unité en tant que Nombre.
     */
    public function testUnityAsNumber(): void
    {
        $unity = Unity::NUMBER;

        self::AssertTrue($unity->isNumber());
        self::assertSame('U', $unity->value);
        self::assertSame('', $unity->symbol());
        self::assertSame('nombre', $unity->label());
        self::assertSame(0.0, $unity->conversion());
        $this->expectException(\Exception::class);
        $unity->inGram(100);
        $unity->convert(3, Unity::GRAM);
        $unity->convert(3, Unity::LITRE);
    }

    /**
     * Test l'unité en tant que poids.
     */
    public function testUnityAsWeight(): void
    {
        $unity = Unity::GRAM;

        self::AssertFalse($unity->isNumber());
        self::assertSame('g', $unity->value);
        self::assertSame('g', $unity->symbol());
        self::assertSame('gramme(s)', $unity->label());
        self::assertSame(1.0, $unity->conversion());
        self::assertSame(100.0, $unity->inGram(100));
        self::assertSame(1.0, $unity->convert(250, Unity::CUP));
        self::assertSame(8.82, $unity->convert(250, Unity::OUNCE));
        self::assertSame(25.0, $unity->convert(250, Unity::CLITRE));
        $this->expectException(\Exception::class);
        $unity->convert(250, Unity::NUMBER);
    }

    /**
     * Test l'unité en tant que volume.
     */
    public function testUnityAsVolume(): void
    {
        $unity = Unity::CLITRE;

        self::AssertFalse($unity->isNumber());
        self::assertSame('cl', $unity->value);
        self::assertSame('cl', $unity->symbol());
        self::assertSame('centilitre(s)', $unity->label());
        self::assertSame(10.0, $unity->conversion());
        self::assertSame(1000.0, $unity->inGram(100));
        self::assertSame(10.0, $unity->convert(250, Unity::CUP));
        self::assertSame(88.21, $unity->convert(250, Unity::OUNCE));
        self::assertSame(2.50, $unity->convert(250, Unity::LITRE));
        $this->expectException(\Exception::class);
        $unity->convert(250, Unity::NUMBER);
    }
}
