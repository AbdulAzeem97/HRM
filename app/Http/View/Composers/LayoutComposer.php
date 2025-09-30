<?php


namespace App\Http\View\Composers;

use Exception;
use Illuminate\View\View;
use JoeDixon\Translation\Drivers\Translation;

class LayoutComposer {

	private $translation;

	public function __construct(Translation $translation)
	{
		$this->translation = $translation;
	}

	public function compose(View $view)
	{
		$languages = $this->translation->allLanguages();
		$view->with('languages', $languages);

		// Ensure selected_date is always available to prevent undefined variable errors
		if (!$view->offsetExists('selected_date')) {
			$view->with('selected_date', now()->format('F-Y'));
		}
	}

}
