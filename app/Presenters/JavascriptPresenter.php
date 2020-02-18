<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Utils\NbspMacro;


final class JavascriptPresenter extends Nette\Application\UI\Presenter
{

	public function createComponentInput(): Nette\Forms\Form
	{
		$form = new Nette\Application\UI\Form();
		$form->getElementPrototype()->addClass('ajax');
		$form->addSelect('locale', 'locale', array_combine(NbspMacro::LOCALES, NbspMacro::LOCALES))
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
