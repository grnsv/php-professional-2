<?php

namespace Tests\Services;

use Faker\Factory;
use App\Enums\User;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use App\Exceptions\CommandException;
use App\Exceptions\ArgumentException;
use App\Services\ArgumentParserService;

class ArgumentParserServiceTest extends TestCase
{
    private ArgumentParserService $argumentParserService;
    private Generator $faker;

    public function __construct(
        ?string $name = null,
        array $data = [],
        $dataName = '',
        ArgumentParserService $argumentParserService = null,
    ) {
        $this->argumentParserService =  $argumentParserService ?? new ArgumentParserService();
        $this->faker = Factory::create();
        parent::__construct($name, $data, $dataName);
    }

    public function argumentsProvider(): iterable
    {
        $one = $this->faker->userName();
        $two = $this->faker->userName();
        $three = $this->faker->userName();
        return
            [
                [$one, $one],
                [$two, $two],
                [$three, $three],
            ];
    }

    /**
     * @throws ArgumentException
     * @throws CommandException
     * @dataProvider argumentsProvider
     */
    public function testItConvertsArgumentsToStrings(
        string|int $inputValue,
        string|int $expectedValue
    ) {
        $argument = $this->argumentParserService->parseRawInput(
            [
                sprintf('%s=%s', User::FIRST_NAME->value, $inputValue),
            ]
        );

        $firstName = $argument->get(User::FIRST_NAME->value);
        $this->assertEquals($expectedValue, $firstName);
    }

    /**
     * @throws ArgumentException
     * @throws CommandException
     */
    public function testItReturnsArgumentsValueByName()
    {
        $firstName = $this->faker->userName();
        $lastName = $this->faker->word();
        $email = $this->faker->email();

        $argument = $this->argumentParserService->parseRawInput(
            [
                sprintf('%s=%s', User::FIRST_NAME->value, $firstName),
                sprintf('%s=%s', User::LAST_NAME->value, $lastName),
                sprintf('%s=%s', User::EMAIL->value, $email)
            ],
            User::getRequiredFields()
        );

        $result = $argument->get(User::FIRST_NAME->value);
        $this->assertSame($firstName, $result);
    }

    /**
     * @throws ArgumentException
     * @throws CommandException
     */
    public function testItThrowsAnExceptionWhenInputArgumentIsInvalid(): void
    {
        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("Параметры должны быть в формате fieldName=fieldValue");

        $this->argumentParserService->parseRawInput(
            [
                sprintf('%s', User::FIRST_NAME->value),
            ],
        );
    }
}
