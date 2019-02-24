<?php

namespace App\AdminModule\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;
use Tracy\Debugger;


final class UserProfileFormFactory
{
	use Nette\SmartObject;

	const PASSWORD_MIN_LENGTH = 7;

	/** @var FormFactory */
	private $factory;

	/** @var Model\UserManager */
	private $userManager;


	public function __construct(FormFactory $factory, Model\UserManager $userManager)
	{
		$this->factory = $factory;
		$this->userManager = $userManager;
	}


	/**
	 * @return Form
	 */
	public function create(callable $onSuccess)
	{
		$form = $this->factory->create();
		$form->addHidden('id');

		$form->addEmail('email', 'E-mail:')
			->setRequired('Vložte, prosím, svůj e-mail.');
		
		$form->addText('firstname', 'Jméno:')
			 ->addRule(Form::MAX_LENGTH,'Zadaný údaj je příliš dlouhý',50)
			 ->setRequired('Zadejte, prosím, své křestní jméno.');

		$form->addText('surname', 'Příjmení:')
			->addRule(Form::MAX_LENGTH,'Zadaný údaj je příliš dlouhý',50)
			->setRequired('Zadejte, prosím, své příjmení.');

		$form->addText('street', 'Ulice:')
			->addRule(Form::MAX_LENGTH,'Zadaný údaj je příliš dlouhý',50)
			->setRequired(false);

		$form->addText('city', 'Město / obec:')
			->addRule(Form::MAX_LENGTH,'Zadaný údaj je příliš dlouhý',50)
			->setRequired(false);

		$form->addText('postcode', 'PSČ:')
			->addRule(Form::PATTERN, 'PSČ zadejte ve tvaru 61200 nebo 612 00', '[0-9]{3}[ ]?[0-9]{2}')
			->setRequired(false);
	
		$form->addText('phone', 'Telefon:')
			 ->addRule(Form::PATTERN,'Telefonní číslo zadejte ve tvaru +420 123 456 789', '([\+]?\d{3})?([ ]?\d{3}){3}')
			 ->setRequired(false);
		
		$form->addTextArea('info', 'Poznámka:')
			->setRequired(false);

		$form->addUpload('photo')
			->addCondition(Form::FILLED)
			->addRule(Form::IMAGE, 'Soubor musí být JPEG, PNG nebo GIF.')
			->setRequired(false);
		
		$form->addSubmit('send', 'Aktualizovat profil');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			Debugger::barDump($values);
			if ($values['photo']->isImage() and $values['photo']->isOk()) {
				$values['photo_name'] = $values['photo']->getSanitizedName();
				$values['photo'] = $values['photo']->toImage();
			} else {
				unset($values['photo']);
			}       
			try {
				$this->userManager->updateProfile($values);
			} catch (Model\DuplicateNameException $e) {
				$form['username']->addError('Uživatelské jméno již existuje.');
				return;
			}
			$onSuccess();
		};

		return $form;
	}
}
