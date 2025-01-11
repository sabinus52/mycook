<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\ValuesList;

use App\ValuesList\Unity;
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
        $unity = new Unity(Unity::NUMBER);

        self::AssertTrue($unity->isNumber());
        self::assertSame('', (string) $unity);
        self::assertSame('', $unity->getSymbol());
        self::assertSame('nombre', $unity->getLabel());
        self::assertSame(0.0, $unity->getConversion());
        $this->expectException(\Exception::class);
        $unity->getInGram(100);
        $unity->convert(3, Unity::GRAM);
        $unity->convert(3, Unity::LITRE);
    }

    /**
     * Test l'unité en tant que poids.
     */
    public function testUnityAsWeight(): void
    {
        $unity = new Unity(Unity::GRAM);

        self::AssertFalse($unity->isNumber());
        self::assertSame('g', (string) $unity);
        self::assertSame('g', $unity->getSymbol());
        self::assertSame('gramme(s)', $unity->getLabel());
        self::assertSame(1.0, $unity->getConversion());
        self::assertSame(100.0, $unity->getInGram(100));
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
        $unity = new Unity(Unity::CLITRE);

        self::AssertFalse($unity->isNumber());
        self::assertSame('cl', (string) $unity);
        self::assertSame('cl', $unity->getSymbol());
        self::assertSame('centilitre(s)', $unity->getLabel());
        self::assertSame(10.0, $unity->getConversion());
        self::assertSame(1000.0, $unity->getInGram(100));
        self::assertSame(10.0, $unity->convert(250, Unity::CUP));
        self::assertSame(88.21, $unity->convert(250, Unity::OUNCE));
        self::assertSame(2.50, $unity->convert(250, Unity::LITRE));
        $this->expectException(\Exception::class);
        $unity->convert(250, Unity::NUMBER);
    }
}
