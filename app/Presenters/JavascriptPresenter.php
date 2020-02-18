<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Prosky\Nbsp\Nbsp;


final class JavascriptPresenter extends Nette\Application\UI\Presenter
{

	protected function beforeRender(): void
	{
		parent::beforeRender();
		$this->template->tasks = (new Nbsp())->getTasks();
	}

	public function createComponentInput(): Nette\Forms\Form
	{
		$form = new Nette\Application\UI\Form();
		$form->getElementPrototype()->addClass('ajax');
		$form->addSelect('locale', 'locale', array_combine(Nbsp::LOCALES, Nbsp::LOCALES))
			->setHtmlAttribute('class', 'form-control')->setRequired();
		$form->addTextArea('text', 'text', 100, 10)
			->setHtmlAttribute('class', 'form-control')
			->setRequired();
		$form->addSubmit('submit')
			->setHtmlAttribute('class', 'btn btn-primary');
		return $form;
	}

	public function createComponentOutput(): Nette\Forms\Form
	{
		$form = new Nette\Application\UI\Form();
		$form->addTextArea('text', 'text', 100, 10)
			->setHtmlAttribute('readonly')
			->setHtmlAttribute('class', 'form-control');
		return $form;
	}

}
