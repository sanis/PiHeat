<?php

namespace App\Command;

use Ballen\GPIO\Adapters\RPiAdapter;
use Ballen\GPIO\Exceptions\GPIOException;
use Ballen\GPIO\GPIO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PihomeGpioTestCommand extends Command
{
    protected static $defaultName = 'pihome:gpio:test';

    protected function configure()
    {
        $this
            ->setDescription('Runs through all pins and flashes them one by one');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $gpio = new GPIO(new RPiAdapter());

        foreach (GPIO::PINS as $pinId) {
            $pins[$pinId] = $gpio->pin($pinId, GPIO::OUT);
        }

        while (true) {
            foreach ($pins as $pinId => $pin) {
                try {
                    if ($pin->getValue() === GPIO::HIGH) {
                        $io->text('PIN: ' . $pinId . ' is ON');
                        $pin->setValue(GPIO::LOW);
                    } else {
                        $io->text('PIN: ' . $pinId . ' is OFF');
                        $pin->setValue(GPIO::HIGH);
                    }
                } catch (GPIOException $exception) {
                    $io->caution($exception->getMessage());
                }
                sleep(1);
            }
            $gpio->clear();
            sleep(2);
        }
    }
}
