<?php

namespace Samyoul\U2F\U2FServer\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Samyoul\U2F\U2FServer\U2FServer;

class U2FServerTest extends TestCase
{
    protected static function getClassMethod(string $className, string $methodName)
    {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    public function testCreateChallenge(): void
    {
        $u2f = self::getClassMethod(U2FServer::class, 'createChallenge');
        $challengeResult = $u2f->invokeArgs(new U2FServer(), []);
        $this->assertNotEmpty($challengeResult);
        $this->assertGreaterThan(20, strlen($challengeResult));
    }

    public function dataProviderForFixSignatureUnusedBits(): array
    {
        return [
            [
                // This is an example certificate in hex format
                // @see https://fidoalliance.org/specs/u2f-specs-1.0-bt-nfc-id-amendment/fido-u2f-raw-message-formats.html
                '3082013c3081e4a003020102020a47901280001155957352300a06082a8648ce3d0403023017311530130603550403130c476e756262792050696c6f74301e170d3132303831343138323933325a170d3133303831343138323933325a3031312f302d0603550403132650696c6f74476e756262792d302e342e312d34373930313238303030313135353935373335323059301306072a8648ce3d020106082a8648ce3d030107034200048d617e65c9508e64bcc5673ac82a6799da3c1446682c258c463fffdf58dfd2fa3e6c378b53d795c4a4dffb4199edd7862f23abaf0203b4b8911ba0569994e101300a06082a8648ce3d0403020347003044022060cdb6061e9c22262d1aac1d96d8c70829b2366531dda268832cb836bcd30dfa0220631b1459f09e6330055722c8d89b7f48883b9089b88d60d1d9795902b30410df',
                '99ab7a0d6a31feb411158184b5acadb8325a2c7e82a55cd709de7771ef6cd3b5',
                '3082013c3081e4a003020102020a47901280001155957352300a06082a8648ce3d0403023017311530130603550403130c476e756262792050696c6f74301e170d3132303831343138323933325a170d3133303831343138323933325a3031312f302d0603550403132650696c6f74476e756262792d302e342e312d34373930313238303030313135353935373335323059301306072a8648ce3d020106082a8648ce3d030107034200048d617e65c9508e64bcc5673ac82a6799da3c1446682c258c463fffdf58dfd2fa3e6c378b53d795c4a4dffb4199edd7862f23abaf0203b4b8911ba0569994e101300a06082a8648ce3d0403020347003044022060cdb6061e9c22262d1aac1d96d8c70829b2366531dda268832cb836bcd30dfa0220631b1459f09e6330055722c8d89b7f48883b9089b88d60d1d9795902b30410df',
            ],
            [
                // This is the "CN=Yubico U2F EE Serial 13831167861" certificate as hex
                // @see https://github.com/privacyidea/privacyidea/blob/v3.4.1/tests/test_lib_tokens_u2f.py#L22
                // After 70d01010b038201010 the 0 is a 2 after transformation
                '3082021c30820106a00302010202043866df75300b06092a864886f70d01010b302e312c302a0603550403132359756269636f2055324620526f6f742043412053657269616c203435373230303633313020170d3134303830313030303030305a180f32303530303930343030303030305a302b3129302706035504030c2059756269636f205532462045452053657269616c2031333833313136373836313059301306072a8648ce3d020106082a8648ce3d03010703420004378dfc740c739b94724ed3d523b9b876810656c13de86fafaecf38d90f55e2c80a1bfe0b30dc53b35de7d045b96dcb8f2bf94fa8e0b903163c7f6edc2e487b71a3123010300e060a2b0601040182c40a01010400300b06092a864886f70d01010b03820101021a4764ca0089cf92adb87fa848538e72cc3efdbb34792943047b8216a939baf4c113562a345b61475979697947bce671aa6a7c06796ed4ebb1b8fd602719b71deb3cf642e98db1d9666ff01e6db74f45af7967c046d6e6ff4b4e09a3141834b69af16465ccdecf3a0a809c0aa49a7b1943f5bd4e3dae3bdccfde6a713a49269eacfb3f9cede0ba79c6bbfba75e6118e20f0f957ea61eed52688226cab42df791037e97eda5e2df6029d2bb7fc327e745e7f9f5862bed29b068cb972a36c86522deb2c7196533335ddfaeb8b6fa0db5026aca845419061aa4d17c070e98fa2fd671d4acd0c290e474a1b4783ec246e0f89a9887c0a4d7a85c662919ba24ea7b9c',
                'dd574527df608e47ae45fbba75a2afdd5c20fd94a02419381813cd55a2a3398f',
                '3082021c30820106a00302010202043866df75300b06092a864886f70d01010b302e312c302a0603550403132359756269636f2055324620526f6f742043412053657269616c203435373230303633313020170d3134303830313030303030305a180f32303530303930343030303030305a302b3129302706035504030c2059756269636f205532462045452053657269616c2031333833313136373836313059301306072a8648ce3d020106082a8648ce3d03010703420004378dfc740c739b94724ed3d523b9b876810656c13de86fafaecf38d90f55e2c80a1bfe0b30dc53b35de7d045b96dcb8f2bf94fa8e0b903163c7f6edc2e487b71a3123010300e060a2b0601040182c40a01010400300b06092a864886f70d01010b03820101001a4764ca0089cf92adb87fa848538e72cc3efdbb34792943047b8216a939baf4c113562a345b61475979697947bce671aa6a7c06796ed4ebb1b8fd602719b71deb3cf642e98db1d9666ff01e6db74f45af7967c046d6e6ff4b4e09a3141834b69af16465ccdecf3a0a809c0aa49a7b1943f5bd4e3dae3bdccfde6a713a49269eacfb3f9cede0ba79c6bbfba75e6118e20f0f957ea61eed52688226cab42df791037e97eda5e2df6029d2bb7fc327e745e7f9f5862bed29b068cb972a36c86522deb2c7196533335ddfaeb8b6fa0db5026aca845419061aa4d17c070e98fa2fd671d4acd0c290e474a1b4783ec246e0f89a9887c0a4d7a85c662919ba24ea7b9c',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForFixSignatureUnusedBits
     */
    public function testFixSignatureUnusedBits(
        string $inputSignatureBlock,
        string $signatureHash,
        string $outputSignatureBlock
    ): void {
        $signature = hex2bin($inputSignatureBlock);
        $this->assertSame($signatureHash, hash('sha256', $signature));
        $u2f = self::getClassMethod(U2FServer::class, 'fixSignatureUnusedBits');
        $output = bin2hex($u2f->invokeArgs(new U2FServer(), [$signature]));
        $this->assertSame($output, $outputSignatureBlock);
    }

    public function testBase64UEncodeDecode(): void
    {
        $shortBlob = 'Salut';
        $u2f = self::getClassMethod(U2FServer::class, 'base64u_encode');
        $encoded = $u2f->invokeArgs(new U2FServer(), [$shortBlob]);
        $this->assertNotEmpty($encoded);
        $this->assertSame('U2FsdXQ', $encoded);
        $u2f = self::getClassMethod(U2FServer::class, 'base64u_decode');
        $decoded = $u2f->invokeArgs(new U2FServer(), [$encoded]);
        $this->assertNotEmpty($decoded);
        $this->assertSame($shortBlob, $decoded);

        $longBlob = '&àçècmm!:************************************************************';
        $longBlob .= '^$ùzefzef:ezf:ze;fzefilqsnéà_è(_yà"tjzifzpofkzof,zlgugealuvnskqjvneruieg';
        $u2f = self::getClassMethod(U2FServer::class, 'base64u_encode');
        $encoded = $u2f->invokeArgs(new U2FServer(), [$longBlob]);
        $this->assertNotEmpty($encoded);
        $this->assertSame(
            'JsOgw6fDqGNtbSE6KioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKio'
                . 'qKioqKioqKioqKioqXiTDuXplZnplZjplemY6emU7ZnplZmlscXNuw6nDoF_DqChfecOgInRqemlmen'
                . 'BvZmt6b2YsemxndWdlYWx1dm5za3Fqdm5lcnVpZWc',
            $encoded
        );
        $u2f = self::getClassMethod(U2FServer::class, 'base64u_decode');
        $decoded = $u2f->invokeArgs(new U2FServer(), [$encoded]);
        $this->assertNotEmpty($decoded);
        $this->assertSame($longBlob, $decoded);
    }

    /**
     * @group openssl
     */
    public function testCheckOpenSSLVersion(): void
    {
        $u2f = self::getClassMethod(U2FServer::class, 'checkOpenSSLVersion');
        $goodVersion = $u2f->invokeArgs(new U2FServer(), []);
        $this->assertTrue($goodVersion);
    }

    /**
     * @group file-system
     */
    public function testGet_certs(): void
    {
        $tempDirName = tempnam(sys_get_temp_dir(), '_testGet_certs');
        unlink($tempDirName); // This is a file for now
        mkdir($tempDirName); // And now a directory
        $tmpFile = tempnam($tempDirName, 'cert_');
        $u2f = self::getClassMethod(U2FServer::class, 'get_certs');
        $filesList = $u2f->invokeArgs(new U2FServer(), [$tempDirName]);
        $this->assertSame([
            $tmpFile
        ], $filesList);
        unlink($tmpFile);
        rmdir($tempDirName);
    }

    public function testPublicKeyToPem(): void
    {
        $u2f = self::getClassMethod(U2FServer::class, 'publicKeyToPem');
        $pemKey = $u2f->invokeArgs(new U2FServer(), [
            ''
        ]);
        $this->assertNull($pemKey);
        $pemKeyWrongStart = $u2f->invokeArgs(new U2FServer(), [
            'd'
        ]);
        $this->assertNull($pemKeyWrongStart);
        $pemKeyWrongLength = $u2f->invokeArgs(new U2FServer(), [
            "\x04"
        ]);
        $this->assertNull($pemKeyWrongLength);
        $pemKeyWrongLength = $u2f->invokeArgs(new U2FServer(), [
            "\x04" . str_repeat('*', 63)
        ]);
        $this->assertNull($pemKeyWrongLength);
        $pemKeyOkay = $u2f->invokeArgs(new U2FServer(), [
            // Public key for "CN=PilotGnubby-0.4.1-47901280001155957352"
            // Value shown by KSE <https://keystore-explorer.org>
            hex2bin('048D617E65C9508E64BCC5673AC82A6799DA3C1446682C258C463FFFDF58DFD2FA3E6C378B53D795C4A4DFFB4199EDD7862F23ABAF0203B4B8911BA0569994E101')
        ]);
        $this->assertSame(
            // Value shown by KSE <https://keystore-explorer.org>
            '-----BEGIN PUBLIC KEY-----' . "\r\n"
                . 'MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEjWF+ZclQjmS8xWc6yCpnmdo8FEZo' . "\r\n"
                . 'LCWMRj//31jf0vo+bDeLU9eVxKTf+0GZ7deGLyOrrwIDtLiRG6BWmZThAQ==' . "\r\n"
                . '-----END PUBLIC KEY-----',
            $pemKeyOkay
        );
    }

    public function testMakeAuthentication(): void
    {
        // Source: https://github.com/Yubico/php-u2flib-server/blob/55d813acf68212ad2cadecde07551600d6971939/tests/u2flib_test.php#L200
        // Data copyright: https://github.com/Yubico/php-u2flib-server/blob/55d813acf68212ad2cadecde07551600d6971939/tests/u2flib_test.php#L3
        $regs = [
            (object) [
                'keyHandle' => 'CTUayZo8hCBeC-sGQJChC0wW-bBg99bmOlGCgw8XGq4dLsxO3yWh9mRYArZxocP5hBB1pEGB3bbJYiM-5acc5w',
                'publicKey' => 'BC0SaFZWC9uH7wamOwduP93kUH2I2hEvyY0Srfj4A258pZSlV0iPoFIH+bd4yhncaqdoPLdEDl5Y\/yaFORPUe3c=',
                'certificate' => 'MIIC4jCBywIBATANBgkqhkiG9w0BAQsFADAdMRswGQYDVQQDExJZdWJpY28gVTJGIFRlc3QgQ0EwHhcNMTQwNTE1'
                    . ' MTI1ODU0WhcNMTQwNjE0MTI1ODU0WjAdMRswGQYDVQQDExJZdWJpY28gVTJGIFRlc3QgRUUwWTATBgcqhkjOPQIBBggqhkjOPQMBBwNCAA'
                    . 'TbCtv1IcdczmPcpuHoJQYNlOYnVBlPnSSvJhq+rZlEH5WjcZEKOiDnPpFeE+i+OAV61XqjfnaQj6\/'
                    . 'iipS2MOudMA0GCSqGSIb3DQEBCwUAA4ICAQCVQGtQYX2thKO064gP4zAPLaIKANklBO5y+mffWFEPC0cCnD5BKUqTrCmFiS2keoEyKFdxAe'
                    . '+oQogWljeR1d\/gj8k8jbDNiXCC7HnTxnhzKTLlq2y9Vp\/VRZHOwd2NZNzpnB9ePNKvUaWCGK\/gN+cynnYFdwJ75iSgMVYb\/'
                    . 'RnFcdPwnsBzBU68hbhTnu\/FvJxWo7rZJ2q7qXpA10eLVXJr4\/4oSXEk9I\/0IIHqOP98Ck\/fAoI5gYI7ygndyqoPJ\/Wkg1VsmjmbFToWY9xb'
                    . '+axbvPefvg+KojwxE6MySMpYh\/h7oKEKamCWk19dJp5jHQmumkHlvQhH\/uUJmyD9EuLmQH+6SmEzZg0Oc9uw1aKamhcNNDCFakJGnv80j1'
                    . '+HbDXnqE0168FBqorS2hmqeaJfNSyg\/SXT950lGC36tLy7BzQ8jYG99Ok32znp0UVbIEEvLSci3JJ0ipLVg\/0J+xOb4zl6a1z65nae4OTj7628\/'
                    . 'UJFmtSU0X6Np9gF1dNizxXPlH0fW1ggRCCQcb5m6ZqrdDJwUx1p7Ydm9AlPyiUwwmN5ADyxmzk\/'
                    . 'AOCoiO96UVvnvUlk2kF7JMNxIv3R0SCzP5fTl7KqGByeA3d7W375o6DWIIEsOI+dJd7pyPXdakecZQRaVubC6\/ICl'
                    . '+G52OEkdp8jYjkDS8j3NAdJ1udNmg==',
                'counter' => 3
            ]
        ];

        $auths = U2FServer::makeAuthentication(
            $regs,
            'http://demo.example.com'
        );
        $this->assertSame(
            [
                [
                    'version' => 'U2F_V2',
                    'challenge' => $auths[0]->challenge(),
                    'keyHandle' => 'CTUayZo8hCBeC-sGQJChC0wW-bBg99bmOlGCgw8XGq4dLsxO3yWh9mRYArZxocP5hBB1pEGB3bbJYiM-5acc5w',
                    'appId' => 'http://demo.example.com'
                ]
            ],
            json_decode(json_encode($auths), true)
        );
    }

    public function testMakeBadAuthentication(): void
    {
        $this->expectExceptionMessage('$registrations of makeAuthentication() method only accepts array of object.');
        $this->expectException(\Exception::class);
        $regs = [
            []
        ];

        U2FServer::makeAuthentication(
            $regs,
            'http://demo.example.com'
        );
    }
}
