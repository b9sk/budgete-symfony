<?php

namespace App\DataFixtures;

use App\Entity\Budget;
use App\Entity\Currency;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use \App\DataFixtures\FixturesData;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    private $fixturesData;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, FixturesData $fixturesData)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->fixturesData = $fixturesData;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadCurrency($manager);
        $this->loadUser($manager);
        $this->loadDemoData($manager);
    }

    public function loadCurrency(ObjectManager $manager)
    {
        $currencyData = $this->fixturesData->getCurrency();

        foreach ($currencyData as $data) {
            $currency = new Currency();
            $currency
                ->setSymbol($data['Symbol'])
                ->setName($data['Name'])
                ->setCode($data['Code'])
            ;
            $manager->persist($currency);
        }

        $manager->flush();
    }

    public function loadUser(ObjectManager $manager)
    {
        $passwd = '123456';

        $user = new User();
        $user
            ->setName('test')
            ->setEmail('test@test.test')
            ->setPassword($this->passwordEncoder->encodePassword($user, $passwd))
            ->setRoles(['ROLE_USER'])
            ->setCurrency($manager->getRepository(Currency::class)->findOneBy(['code' => 'ru']));
        ;

        $this->addReference("{$user->getName()}_user", $user);

        $manager->persist($user);
        $manager->flush();
    }

    public function loadDemoData(ObjectManager $manager)
    {
        // @todo: set todays records
        foreach ([1,2,3,4,5,6,7,8,9,10] as $time) {
            $record = new Budget();
            $record
                ->setCreated(new \DateTime("now -$time hour"))
                ->setAmount(rand(1,9) * 100)
                ->setType(rand(0,2) ? 'expense' : 'income' )
                ->setUser($this->getReference('test_user'))
            ;

            $manager->persist($record);
        }

        // @todo: set records from last three months
        foreach (range(1,99) as $day) {
            $i = 0;
            if ($i < 5) {
                $record = new Budget();
                $record
                    ->setCreated(new \DateTime("now -$day day"))
                    ->setAmount(rand(1,9) * 100)
                    ->setType(rand(0,2) ? 'expense' : 'income' )
                    ->setUser($this->getReference('test_user'))
                ;
                $i++;
            }

            $manager->persist($record);
        }
        
        $manager->flush();
    }
}
