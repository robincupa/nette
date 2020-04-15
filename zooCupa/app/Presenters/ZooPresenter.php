<?php

declare(strict_types = 1);

namespace App\Presenters;

use App\Model;
use Nette\Application\UI;
use Nette\Utils\DateTime;

use Tracy\Debugger;

final class ZooPresenter extends BasePresenter {
    
    private $zooManager;

    public function __construct(Model\ZooManager $zooManager) {
        $this->zooManager = $zooManager;
    }

    public function renderList($order = 'date DESC'): void {
        $this->template->zooList = $this->zooManager->getAll($order);
    }

    public function renderDetail($id): void {
        $this->template->event = $this->zooManager->getById($id);
    }

    public function actionInsert(): void {
        Debugger::log('Vložen nový záznam');
        $dnes = new DateTime();
        $this['zooForm']['type']->setDefaultValue('odborná');
    }

    public function actionUpdate($id): void {
        Debugger::log('Aktualizován záznam ' . $id);
        $data = $this->zooManager->getById($id)->toArray();
        $data['time'] = $data['time']->format('H:i:s');
        $data['date'] = $data['date']->format('Y-m-d');
        Debugger::barDump($data);
        $this['zooForm']->setDefaults($data);
    }

    public function actionDelete($id): void {
        Debugger::log('Odstraněn záznam ' . $id);
        if ($this->zooManager->delete($id)) {
            $this->flashMessage('Záznam byl úspěšně smazán', 'success');
        } else {
            $this->flashMessage('Došlo k nějaké chybě při mazání záznamu', 'danger');
        }
        $this->redirect('list');
    }

    protected function createComponentZooForm(): UI\Form {
            $form = new UI\Form;
            $form->addText('nazev', 'Název akce:');
            $form->addText('date', 'Datum konání:')
                ->setHtmlType('date');
            $form->addText('time', 'Čas konání:')
                    ->setHtmlType('time');
            $form->addText('popis','Popis akce:');
            $type = [
                'tradiční' => 'tradiční',
                'ostatní' => 'ostatní',
                'charitativní' => 'charitativní',
                'odborná' => 'odborná'
            ];
            $form->addSelect('type','Typ akce:', $type);
            $form->addText('visitors', 'Počet návštěvníků:')
                ->setHtmlType('number')
                ->setHtmlAttribute('min', '1')
                ->setHtmlAttribute('max', '999999')
                ->setHtmlAttribute('step', '1')
                ->addRule(UI\Form::RANGE, 'Počet návštěvníků může být maximálně šestimístný.', [1, 999999]);
            $form->addSubmit('submit','Odeslat');
            $form->onSuccess[] = [$this,'zooFormSucceeded'];
            return $form;
        }
        
    public function zooFormSucceeded(UI\Form $form, $values): void {
        Debugger::barDump($values);
        $eventId = $this->getParameter('id');
        if ($eventId) {
            $event = $this->zooManager->update($eventId, $values);
        } else {
            $event = $this->zooManager->insert($values);
        }
        if ($event) {
            $this->flashMessage('Akce byla úspěšně uložena', 'success');
        } else {
            $this->flashMessage('Došlo k nějaké chybě při ukládání do databáze', 'danger');
        }
        $this->redirect('Zoo:list');
    }

}
