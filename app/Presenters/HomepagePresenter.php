<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Prosky\Nbsp\Nbsp;
use App\Utils\NbspMacro;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{

	/**
	 * @var Nbsp
	 */
	private $nbsp;

	protected function beforeRender(): void
	{
		parent::beforeRender();
		$this->template->tasks = (new Nbsp())->getTasks();
	}

	public function createComponentInput(): Nette\Forms\Form
	{

		$options = [
			'showInvisibles' => true,
			'mode' => 'xml',
			'lineWrapping' => true,
			'theme' => 'one-dark'
		];
		$form = new Nette\Application\UI\Form();
		$form->getElementPrototype()->addClass('ajax');
		$form->addSelect('locale', 'locale', array_combine(Nbsp::LOCALES, Nbsp::LOCALES))
			->setHtmlAttribute('class', 'form-control')
			->setRequired();
		$form->addTextArea('text', 'text', 100, 10)
			->setHtmlAttribute('class', 'form-control')
			->setHtmlAttribute('data-code-mirror', $options)
			->setRequired();


		$form->addSubmit('submit')
			->setHtmlAttribute('class', 'btn btn-primary');
		$form->onSubmit[] = function (Nette\Forms\Form $form) {
			bdump($form->getValues());
			$this->redrawControl('forms');
		};
		$form->onError[] = function (Nette\Forms\Form $form) {
			bdump($form->getErrors());
		};
		$form->onSuccess[] = function (Nette\Forms\Form $form, Nette\Utils\ArrayHash $values) {
			$nbsp = new Nbsp;
			$this['output']->setValues([
				'text' => htmlentities($nbsp->nbsp($values->text, $values->locale))
			]);
		};
		return $form;
	}

	public function createComponentOutput(): Nette\Forms\Form
	{
		$options = [
			'mode' => 'xml',
			'readOnly' => true,
			'theme' => 'one-dark',
			'lineWrapping' => true,
			'showInvisibles' => true,
		];
		$form = new Nette\Application\UI\Form();
		$form->addTextArea('text', 'text', 100, 10)
			->setHtmlAttribute('readonly')
			->setHtmlAttribute('class', 'form-control')
			->setHtmlAttribute('data-code-mirror', $options);
		return $form;
	}

}
