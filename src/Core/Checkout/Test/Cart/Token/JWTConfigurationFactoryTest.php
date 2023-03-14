<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Token;

use Lcobucci\JWT\Configuration;
use League\OAuth2\Server\CryptKey;
use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Payment\Cart\Token\JWTConfigurationFactory;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

/**
 * @internal
 */
#[Package('checkout')]
class JWTConfigurationFactoryTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testWithFile(): void
    {
        $signer = $this->getContainer()->get('laser.jwt_signer');
        $privateKey = $this->getContainer()->get('laser.private_key');
        $publicKey = $this->getContainer()->get('laser.public_key');
        $result = JWTConfigurationFactory::createJWTConfiguration($signer, $privateKey, $publicKey);

        static::assertInstanceOf(Configuration::class, $result);
    }

    public function testWithInMemoryKey(): void
    {
        $signer = $this->getContainer()->get('laser.jwt_signer');
        $privateKey = $this->getContainer()->get('laser.private_key');
        $publicKey = $this->getContainer()->get('laser.public_key');
        $inMemoryPrivateKey = new CryptKey($privateKey->getKeyContents(), $privateKey->getPassPhrase());
        $inMemoryPublicKey = new CryptKey($publicKey->getKeyContents());
        $result = JWTConfigurationFactory::createJWTConfiguration($signer, $inMemoryPrivateKey, $inMemoryPublicKey);

        static::assertInstanceOf(Configuration::class, $result);
    }
}
