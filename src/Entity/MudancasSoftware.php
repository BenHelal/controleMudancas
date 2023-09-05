<?php

namespace App\Entity;

use App\Repository\MudancasSoftwareRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MudancasSoftwareRepository::class)]
class MudancasSoftware
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    /*********************
     * Solicitante
     ********************/

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numberRef = null;
 
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ref = null;

    /*******************
     * Gerente de aprovação
     * **********************/
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $priority = null;

    #[ORM\ManyToOne]
    private ?Person $gestor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $date = null;

    /*******************
     * Gestor da Mudança
     * **********************/

     #[ORM\Column(length: 255, nullable: true)]
     private ?string $priorityGestor = null;
 
     #[ORM\Column(length: 255, nullable: true)]
     private ?string $iniciar = null;
 
     #[ORM\Column(length: 255, nullable: true)]
     private ?string $dateInicio = null;
 
     #[ORM\OneToMany(mappedBy: 'mudancasSoftware', targetEntity: StepsGestor::class)]
    private Collection $stepsGestor;

    /*******************
     * Aceite Solicitante
     * **********************/
     #[ORM\Column(length: 255, nullable: true)]
     private ?string $DocsSolicitante = null;

    /*******************
     * Desenvolvimento e controle de fases
     * **********************/
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $DocsDev = null;

    #[ORM\OneToMany(mappedBy: 'mudancasSoftware', targetEntity: StepsDev::class)]
    private Collection $stepDev;


    /*******************
     * Casos de Testes TI
     *************************/
    #[ORM\OneToMany(mappedBy: 'mudancasSoftware', targetEntity: StepsTest::class)]
    private Collection $stepsTest;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $docTest = null;

    #[ORM\OneToMany(mappedBy: 'mudancasSoftware', targetEntity: StepsTestSol::class)]
    private Collection $stepsTestSol;

    /*******************
     * Casos de Testes Solicitante
     *************************/
    /**
     * @Assert\File(
     *     maxSize="1024m",
     *     mimeTypes={"application/pdf","application/msword","application/vnd.ms-excel"
     * ,"application/vnd.oasis.opendocument.text","application/vnd.oasis.opendocument.text-flat-xml","application/vnd.oasis.opendocument.text-template","application/vnd.oasis.opendocument.text-web","application/vnd.oasis.opendocument.text-master","application/vnd.oasis.opendocument.graphics","application/vnd.oasis.opendocument.graphics-flat-xml","application/vnd.oasis.opendocument.graphics-template","application/vnd.oasis.opendocument.presentation","application/vnd.oasis.opendocument.presentation-flat-xml","application/vnd.oasis.opendocument.presentation-template","application/vnd.oasis.opendocument.spreadsheet","application/vnd.oasis.opendocument.spreadsheet-flat-xml","application/vnd.oasis.opendocument.spreadsheet-template","application/vnd.oasis.opendocument.chart","application/vnd.oasis.opendocument.formula","application/vnd.oasis.opendocument.image","application/vnd.sun.xml.writer","application/vnd.sun.xml.writer.template","application/vnd.sun.xml.writer.global","application/vnd.stardivision.writer","application/vnd.stardivision.writer-global","application/vnd.sun.xml.calc","application/vnd.sun.xml.calc.template","application/vnd.stardivision.calc","application/vnd.stardivision.chart","application/vnd.sun.xml.impress","application/vnd.sun.xml.impress.template","application/vnd.stardivision.impress","application/vnd.sun.xml.draw","application/vnd.sun.xml.draw.template","application/vnd.stardivision.draw","application/vnd.sun.xml.math","application/vnd.stardivision.math","application/vnd.sun.xml.base","application/vnd.openofficeorg.extension","application/vnd.openxmlformats-officedocument.wordprocessingml.document","application/vnd.ms-word.document.macroenabled.12","application/vnd.openxmlformats-officedocument.wordprocessingml.template","application/vnd.ms-word.template.macroenabled.12","application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/vnd.ms-excel.sheet.macroenabled.12","application/vnd.openxmlformats-officedocument.spreadsheetml.template","application/vnd.ms-excel.template.macroenabled.12","application/vnd.openxmlformats-officedocument.presentationml.presentation","application/vnd.ms-powerpoint.presentation.macroenabled.12","application/vnd.openxmlformats-officedocument.presentationml.template","application/vnd.ms-powerpoint.template.macroenabled.12"},
     *     mimeTypesMessage="Please upload a valid PDF file",
     *     maxSizeMessage="This PDF file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}."
     * )
     */   
    #[ORM\Column(length: 255, nullable: true)]
    private $docTestSol = null;


    // permission 
    #[ORM\ManyToMany(targetEntity: person::class)]
    #[JoinTable(name: 'developers_mud')]
    private Collection $developers;

    
    #[ORM\ManyToMany(targetEntity: Person::class)]
    #[JoinTable(name: 'testers_ti')]
    private Collection $testersti;

    #[ORM\ManyToMany(targetEntity: Person::class)]
    #[JoinTable(name: 'testers_mud')]
    private Collection $testers;
     
    public function __construct()
    {
        $this->stepsGestor = new ArrayCollection();
        $this->stepDev = new ArrayCollection();
        $this->stepsTest = new ArrayCollection();
        $this->stepsTestSol = new ArrayCollection();
        $this->developers = new ArrayCollection();
        $this->testers = new ArrayCollection();
        $this->testersti = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getNumberRef(): ?string
    {
        return $this->numberRef;
    }

    public function setNumberRef(string $numberRef): self
    {
        $this->numberRef = $numberRef;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPriorityGestor(): ?string
    {
        return $this->priorityGestor;
    }

    public function setPriorityGestor(?string $priorityGestor): self
    {
        $this->priorityGestor = $priorityGestor;

        return $this;
    }

    public function getIniciar(): ?string
    {
        return $this->iniciar;
    }

    public function setIniciar(?string $iniciar): self
    {
        $this->iniciar = $iniciar;

        return $this;
    }

    public function getDateInicio(): ?string
    {
        return $this->dateInicio;
    }

    public function setDateInicio(?string $dateInicio): self
    {
        $this->dateInicio = $dateInicio;

        return $this;
    }

    public function getDocsSolicitante(): ?string
    {
        return $this->DocsSolicitante;
    }

    public function setDocsSolicitante(?string $DocsSolicitante): self
    {
        $this->DocsSolicitante = $DocsSolicitante;

        return $this;
    }

    public function getDocsDev(): ?string
    {
        return $this->DocsDev;
    }

    public function setDocsDev(string $DocsDev): self
    {
        $this->DocsDev = $DocsDev;

        return $this;
    }

    public function getGestor(): ?Person
    {
        return $this->gestor;
    }

    public function setGestor(?Person $gestor): self
    {
        $this->gestor = $gestor;

        return $this;
    }

    /**
     * @return Collection<int, StepsGestor>
     */
    public function getStepsGestor(): Collection
    {
        return $this->stepsGestor;
    }

    public function addStepsGestor(StepsGestor $stepsGestor): self
    {
        if (!$this->stepsGestor->contains($stepsGestor)) {
            $this->stepsGestor->add($stepsGestor);
            $stepsGestor->setMudancasSoftware($this);
        }

        return $this;
    }

    public function removeStepsGestor(StepsGestor $stepsGestor): self
    {
        if ($this->stepsGestor->removeElement($stepsGestor)) {
            // set the owning side to null (unless already changed)
            if ($stepsGestor->getMudancasSoftware() === $this) {
                $stepsGestor->setMudancasSoftware(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StepsDev>
     */
    public function getStepDev(): Collection
    {
        return $this->stepDev;
    }

    public function addStepDev(StepsDev $stepDev): self
    {
        if (!$this->stepDev->contains($stepDev)) {
            $this->stepDev->add($stepDev);
            $stepDev->setMudancasSoftware($this);
        }

        return $this;
    }

    public function removeStepDev(StepsDev $stepDev): self
    {
        if ($this->stepDev->removeElement($stepDev)) {
            // set the owning side to null (unless already changed)
            if ($stepDev->getMudancasSoftware() === $this) {
                $stepDev->setMudancasSoftware(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StepsTest>
     */
    public function getStepsTest(): Collection
    {
        return $this->stepsTest;
    }

    public function addStepsTest(StepsTest $stepsTest): self
    {
        if (!$this->stepsTest->contains($stepsTest)) {
            $this->stepsTest->add($stepsTest);
            $stepsTest->setMudancasSoftware($this);
        }

        return $this;
    }

    public function removeStepsTest(StepsTest $stepsTest): self
    {
        if ($this->stepsTest->removeElement($stepsTest)) {
            // set the owning side to null (unless already changed)
            if ($stepsTest->getMudancasSoftware() === $this) {
                $stepsTest->setMudancasSoftware(null);
            }
        }

        return $this;
    }

    public function getDocTest()
    {
        return $this->docTest;
    }

    public function setDocTest( $docTest): self
    {
        $this->docTest = $docTest;

        return $this;
    }

    public function getDocTestSol()
    {
        return $this->docTestSol;
    }

    public function setDocTestSol($docTestSol): self
    {
        $this->docTestSol = $docTestSol;

        return $this;
    }

    /**
     * @return Collection<int, StepsTestSol>
     */
    public function getStepsTestSol(): Collection
    {
        return $this->stepsTestSol;
    }

    public function addStepsTestSol(StepsTestSol $stepsTestSol): self
    {
        if (!$this->stepsTestSol->contains($stepsTestSol)) {
            $this->stepsTestSol->add($stepsTestSol);
            $stepsTestSol->setMudancasSoftware($this);
        }

        return $this;
    }

    public function removeStepsTestSol(StepsTestSol $stepsTestSol): self
    {
        if ($this->stepsTestSol->removeElement($stepsTestSol)) {
            // set the owning side to null (unless already changed)
            if ($stepsTestSol->getMudancasSoftware() === $this) {
                $stepsTestSol->setMudancasSoftware(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, person>
     */
    public function getDevelopers(): Collection
    {
        return $this->developers;
    }

    public function addDeveloper(person $developer): self
    {
        if (!$this->developers->contains($developer)) {
            $this->developers->add($developer);
        }

        return $this;
    }

    public function removeDeveloper(person $developer): self
    {
        $this->developers->removeElement($developer);

        return $this;
    }

        /**
     * @return Collection<int, Person>
     */
    public function getTestersti(): Collection
    {
        return $this->testers;
    }

    public function addTestersti(Person $testersti): self
    {
        if (!$this->testersti->contains($testersti)) {
            $this->testersti->add($testersti);
        }

        return $this;
    }

    public function removeTestersti(Person $testersti): self
    {
        $this->testersti->removeElement($testersti);

        return $this;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getTesters(): Collection
    {
        return $this->testers;
    }

    public function addTester(Person $tester): self
    {
        if (!$this->testers->contains($tester)) {
            $this->testers->add($tester);
        }

        return $this;
    }

    public function removeTester(Person $tester): self
    {
        $this->testers->removeElement($tester);

        return $this;
    }

}
