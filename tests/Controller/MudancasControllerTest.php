<?php

namespace App\Test\Controller;

use App\Entity\Mudancas;
use App\Repository\MudancasRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MudancasControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private MudancasRepository $repository;
    private string $path = '/mudanca/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Mudancas::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Mudanca index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'mudanca[nomeMudanca]' => 'Testing',
            'mudanca[descMudanca]' => 'Testing',
            'mudanca[descImpacto]' => 'Testing',
            'mudanca[descImpactoArea]' => 'Testing',
            'mudanca[justif]' => 'Testing',
            'mudanca[areaResp]' => 'Testing',
            'mudanca[areaImpact]' => 'Testing',
        ]);

        self::assertResponseRedirects('/mudanca/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Mudancas();
        $fixture->setNomeMudanca('My Title');
        $fixture->setDescMudanca('My Title');
        $fixture->setDescImpacto('My Title');
        $fixture->setDescImpactoArea('My Title');
        $fixture->setJustif('My Title');
        $fixture->setAreaResp('My Title');
        $fixture->setAreaImpact('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Mudanca');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Mudancas();
        $fixture->setNomeMudanca('My Title');
        $fixture->setDescMudanca('My Title');
        $fixture->setDescImpacto('My Title');
        $fixture->setDescImpactoArea('My Title');
        $fixture->setJustif('My Title');
        $fixture->setAreaResp('My Title');
        $fixture->setAreaImpact('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'mudanca[nomeMudanca]' => 'Something New',
            'mudanca[descMudanca]' => 'Something New',
            'mudanca[descImpacto]' => 'Something New',
            'mudanca[descImpactoArea]' => 'Something New',
            'mudanca[justif]' => 'Something New',
            'mudanca[areaResp]' => 'Something New',
            'mudanca[areaImpact]' => 'Something New',
        ]);

        self::assertResponseRedirects('/mudanca/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getNomeMudanca());
        self::assertSame('Something New', $fixture[0]->getDescMudanca());
        self::assertSame('Something New', $fixture[0]->getDescImpacto());
        self::assertSame('Something New', $fixture[0]->getDescImpactoArea());
        self::assertSame('Something New', $fixture[0]->getJustif());
        self::assertSame('Something New', $fixture[0]->getAreaResp());
        self::assertSame('Something New', $fixture[0]->getAreaImpact());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Mudancas();
        $fixture->setNomeMudanca('My Title');
        $fixture->setDescMudanca('My Title');
        $fixture->setDescImpacto('My Title');
        $fixture->setDescImpactoArea('My Title');
        $fixture->setJustif('My Title');
        $fixture->setAreaResp('My Title');
        $fixture->setAreaImpact('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/mudanca/');
    }
}
