<?php

namespace App\FrontModule\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;
use Tracy\Debugger;


final class SignUpFormFactory
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
		$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Zadejte, prosím, uživatelské jméno.');

		$form->addEmail('email', 'E-mail:')
			->setRequired('Vložte, prosím, svůj e-mail.');

		$passwordInput = $form->addPassword('password', 'Heslo:')
			->setOption('description', sprintf('minimálně %d znaků', self::PASSWORD_MIN_LENGTH))
			->setRequired('Zadejte, prosím, heslo')
			->addRule($form::MIN_LENGTH, null, self::PASSWORD_MIN_LENGTH);
			
		$form->addPassword('password2', 'Zopakujte heslo')
			->setRequired('Zopakujte, prosím, heslo kvůli ověření')
			->addRule($form::EQUAL, 'Zadaná hesla se neshodují!', $passwordInput);
		
		$form->addText('firstname', 'Jméno:')
			 ->addRule(Form::MAX_LENGTH,'Zadaný údaj je příliš dlouhý',50)
			 ->setRequired('Zadejte, prosím, své křestní jméno.');

		$form->addText('surname', 'Příjmení:')
			->addRule(Form::MAX_LENGTH,'Zadaný údaj je příliš dlouhý',50)
			->setRequired('Zadejte, prosím, své příjmení.');

		$form->addSubmit('send', 'Zaregistrovat se');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			Debugger::barDump($values);
			try {
				$this->userManager->add($values);
			} catch (Model\DuplicateNameException $e) {
				$form['username']->addError('Uživatelské jméno již existuje.');
				return;
			}
			$onSuccess();
		};

		return $form;
	}
}
