<?
namespace Auth;

use App\Filament\Auth;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as AuthRegister;

class Register extends AuthRegister {

    public  function form(Form $form): Form {
        return $form->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        TextInput::make('telephone')

                    ])
                    ->statePath('data');

        
    }

    

}
