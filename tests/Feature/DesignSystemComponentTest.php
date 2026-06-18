<?php

use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('renders button with primary variant by default', function () {
    $view = $this->blade('<x-button>Submit</x-button>');

    $view->assertSee('Submit');
    $view->assertSee('bg-primary-600');
});

it('renders button with danger variant', function () {
    $view = $this->blade('<x-button variant="danger">Supprimer</x-button>');

    $view->assertSee('bg-danger-600');
});

it('renders button with outline variant', function () {
    $view = $this->blade('<x-button variant="outline">Annuler</x-button>');

    $view->assertSee('border');
    $view->assertSee('border-primary-600');
});

it('renders button with ghost variant', function () {
    $view = $this->blade('<x-button variant="ghost">Lien</x-button>');

    $view->assertSee('bg-transparent');
});

it('renders button with sm size', function () {
    $view = $this->blade('<x-button size="sm">Petit</x-button>');

    $view->assertSee('px-3');
    $view->assertSee('py-1');
});

it('renders button with lg size', function () {
    $view = $this->blade('<x-button size="lg">Grand</x-button>');

    $view->assertSee('px-6');
    $view->assertSee('py-3');
});

it('renders button with disabled state', function () {
    $view = $this->blade('<x-button disabled>Valider</x-button>');

    $view->assertSee('disabled');
    $view->assertSee('opacity-50');
});

it('renders button with additional class', function () {
    $view = $this->blade('<x-button class="w-full">Valider</x-button>');

    $view->assertSee('w-full');
});

it('renders input with label', function () {
    $view = $this->blade('<x-input name="email" label="Email" />');

    $view->assertSee('Email');
    $view->assertSeeHtml('id="email"');
    $view->assertSeeHtml('name="email"');
});

it('renders input with error message', function () {
    $view = $this->blade('<x-input name="email" label="Email" message="Ce champ est requis" />');

    $view->assertSee('border-danger-500');
    $view->assertSee('Ce champ est requis');
});

it('renders input with help text', function () {
    $view = $this->blade('<x-input name="email" label="Email" help="Nous ne partagerons jamais votre email" />');

    $view->assertSee('Nous ne partagerons jamais votre email');
    $view->assertSee('text-neutral-500');
});

it('renders input with password type', function () {
    $view = $this->blade('<x-input name="password" type="password" />');

    $view->assertSeeHtml('type="password"');
});

it('renders card with content', function () {
    $view = $this->blade('<x-card>Contenu de la carte</x-card>');

    $view->assertSee('Contenu de la carte');
    $view->assertSee('shadow-card');
    $view->assertSee('rounded-lg');
});

it('renders card with header', function () {
    $view = $this->blade('<x-card header="Titre de la carte">Contenu</x-card>');

    $view->assertSee('Titre de la carte');
    $view->assertSee('border-b');
});

it('renders card with footer', function () {
    $view = $this->blade('<x-card footer="Actions du footer">Contenu</x-card>');

    $view->assertSee('Actions du footer');
    $view->assertSee('border-t');
});

it('renders table with headers and rows', function () {
    $headers = [
        ['key' => 'name', 'label' => 'Nom'],
        ['key' => 'email', 'label' => 'Email'],
    ];
    $rows = [
        ['name' => 'Jean Dupont', 'email' => 'jean@example.com'],
    ];

    $view = $this->blade('<x-table :headers="$headers" :rows="$rows" />', compact('headers', 'rows'));

    $view->assertSee('Nom');
    $view->assertSee('Email');
    $view->assertSee('Jean Dupont');
    $view->assertSee('jean@example.com');
});

it('renders table empty state', function () {
    $headers = [['key' => 'name', 'label' => 'Nom']];
    $rows = [];

    $view = $this->blade('<x-table :headers="$headers" :rows="$rows" />', compact('headers', 'rows'));

    $view->assertSee('Aucun résultat');
});

it('renders badge with success variant', function () {
    $view = $this->blade('<x-badge variant="success">Validé</x-badge>');

    $view->assertSee('Validé');
    $view->assertSee('bg-success-100');
    $view->assertSee('text-success-800');
});

it('renders badge with danger variant', function () {
    $view = $this->blade('<x-badge variant="danger">Rejeté</x-badge>');

    $view->assertSee('bg-danger-100');
});

it('renders alert with success type', function () {
    $view = $this->blade('<x-alert type="success">Opération réussie</x-alert>');

    $view->assertSee('Opération réussie');
    $view->assertSeeHtml('role="alert"');
    $view->assertSee('bg-success-50');
});

it('renders alert with dismissible button', function () {
    $view = $this->blade('<x-alert type="warning" dismissible>Attention</x-alert>');

    $view->assertSee('x-on:click');
    $view->assertSeeHtml('aria-label="Fermer"');
});

it('renders progress bar with percentage', function () {
    $view = $this->blade('<x-progress :value="75" />');

    $view->assertSee('width: 75%');
});

it('renders progress bar with success variant', function () {
    $view = $this->blade('<x-progress :value="90" variant="success" />');

    $view->assertSee('bg-success-500');
});

it('renders progress bar with label', function () {
    $view = $this->blade('<x-progress :value="50" label="Analyse en cours" />');

    $view->assertSee('Analyse en cours');
    $view->assertSee('50%');
});
