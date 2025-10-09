# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Backend
- `composer run dev` - Start development environment (Laravel server + queue + logs + Vite)
- `composer run dev:ssr` - Start with SSR support 
- `composer run test` - Run full test suite
- `php artisan test --filter=testName` - Run specific test
- `vendor/bin/pint --dirty` - Format PHP code (required before commits)

### Frontend  
- `npm run dev` - Start Vite development server
- `npm run build` - Build for production
- `npm run build:ssr` - Build with SSR support
- `npm run lint` - Run ESLint with auto-fix
- `npm run format` - Format code with Prettier
- `npm run format:check` - Check formatting

### Docker
- Use `docker compose` instead of `docker-compose`

## Architecture Overview

This is a Laravel 12 + Vue 3 + Inertia.js application with the following key architectural patterns:

### Backend Structure
- **Modern Laravel 12**: Uses streamlined structure (no Kernel.php, commands auto-register)
- **Inertia.js Integration**: Server-side rendering with `Inertia::render()`
- **Laravel Fortify**: Authentication system with 2FA support
- **Wayfinder**: Type-safe routing between Laravel and Vue
- **Pest Testing**: v4 with browser testing capabilities

### Frontend Structure  
- **Vue 3 + TypeScript**: Located in `resources/js/`
- **Reka UI Components**: Modern component library with Tailwind CSS v4
- **Route-based Organization**: Controllers mirror frontend routes in `resources/js/routes/`
- **Type-safe Actions**: Auto-generated from Laravel controllers in `resources/js/actions/`

### Key Directories
- `resources/js/pages/` - Inertia page components
- `resources/js/layouts/` - Layout components (App, Auth, Settings)  
- `resources/js/components/ui/` - Reka UI component library
- `resources/js/routes/` - Route-specific logic
- `resources/js/actions/` - Type-safe Laravel controller bindings
- `app/Http/Controllers/` - Laravel controllers (Auth, Settings)
- `tests/` - Pest tests (Feature and Unit)

### Authentication Flow
- Laravel Fortify handles auth logic
- Inertia provides seamless SPA navigation
- 2FA support with recovery codes
- Settings pages for profile, password, appearance

## Plan de Développement - Plateforme de Services à Domicile

### Vue d'ensemble
Création d'une plateforme de mise en relation prestataires/clients inspirée de Wecasa.fr, avec design moderne vert pastel/marron sur architecture Laravel 12 + Vue 3 + Inertia.js.

## 🚀 Plan de Déploiement avec Coolify

### Configuration Actuelle
- **Environnement de développement** : Laravel Sail avec MySQL 8.0 et Redis
- **Base de données** : Migration complète de SQLite vers MySQL réussie
- **Containers actifs** :
  - Laravel (port 8080)
  - MySQL (port 3307)  
  - Redis (port 6380)

### Fichiers de Déploiement Créés
1. **docker-compose.prod.yml** - Configuration production avec 5 services :
   - `app` : Application Laravel principale (port 80)
   - `queue` : Worker pour tâches en arrière-plan
   - `scheduler` : Cron jobs Laravel
   - `mysql` : Base de données MySQL 8.0
   - `redis` : Cache et sessions

2. **Dockerfile.prod** - Image Docker optimisée pour production :
   - PHP 8.4-FPM Alpine
   - Nginx avec configuration optimisée
   - Supervisor pour gestion des processus
   - Extensions PHP requises (PDO MySQL, Redis)

3. **deploy.sh** - Script de déploiement automatisé :
   - Installation des dépendances
   - Build des assets frontend
   - Migrations de base de données
   - Optimisations Laravel (cache, routes, vues)
   - Configuration des permissions

4. **.dockerignore** - Exclusion des fichiers non nécessaires
5. **.env.prod.example** - Template des variables d'environnement
6. **DEPLOY.md** - Documentation complète du déploiement

### Variables d'Environnement Coolify
```env
# Application
APP_NAME=ServicesPro
APP_ENV=production
APP_URL=https://votre-domaine.com

# Base de données
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=laravelvueservices
DB_USERNAME=servicespro
DB_PASSWORD=mot_de_passe_sécurisé

# Cache et sessions
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis

# Premier déploiement
INITIAL_DEPLOY=true
```

### Workflow de Déploiement
1. **Push vers Git** → Coolify détecte automatiquement
2. **Build Docker** → Construction de l'image production
3. **Déploiement** → `deploy.sh` configure l'environnement
4. **Validation** → Health checks vérifient le fonctionnement

### Tests de Connectivité Validés ✅
- **Database** : `mysql via TCP/IP` - Connecté
- **Redis** : `OK` - Fonctionnel
- **Application** : Serveur de développement actif

### Prochaines Étapes
1. Configuration du domaine dans Coolify
2. Ajout des variables d'environnement
3. Premier déploiement avec `INITIAL_DEPLOY=true`
4. Tests de production

---

## Plan de Développement - Plateforme de Services à Domicile

### PHASE 1 - FONDATIONS (2-3 semaines)

#### Système de rôles et permissions
- Installation spatie/laravel-permission
- Création des rôles : client, prestataire, admin
- Middlewares de protection des routes
- Permissions granulaires par fonctionnalité

#### Base de données
- Migration users : user_type, phone, address, verification_status
- Migration categories : name, description, icon, status, parent_id
- Migration services : title, description, price_min, price_max, duration, category_id
- Migration user_profiles : bio, experience, certifications, availability_json
- Migration booking_requests : status, scheduled_date, price, notes, client_id, provider_id
- Seeders pour données de démonstration

#### Design System
- Configuration Tailwind CSS v4 avec palette vert pastel (#A8E6A3, #7DD87D) et marron (#8B4513, #A0522D)
- Composants UI de base : Button, Card, Input, Modal, Badge
- Layout principal avec navigation adaptative selon rôle
- Responsive design mobile-first

### PHASE 2 - GESTION PRESTATAIRES (2-3 semaines)

#### Inscription et validation
- ProviderRegistrationController avec validation KYC
- Formulaire multi-étapes avec upload de documents
- Système de validation admin des prestataires
- Interface de gestion du statut d'approbation

#### Gestion des services
- CRUD complet des services proposés
- Gestion des tarifs et forfaits
- Upload de galerie photos/vidéos
- Gestion des zones d'intervention

#### Dashboard prestataire
- Statistiques (revenus, réservations, avis)
- Calendrier des disponibilités
- Gestion des demandes en attente

### PHASE 3 - INTERFACE CLIENT (2-3 semaines)

#### Recherche et navigation
- Page d'accueil avec moteur de recherche intelligent
- Filtres avancés : catégorie, prix, localisation, notes, disponibilité
- Système de géolocalisation avec cartes interactives
- Pages de catégories organisées

#### Profils prestataires
- Pages détaillées avec galerie, avis, tarifs
- Comparateur de prestataires
- Système de favoris
- Calendrier de disponibilités public

#### Demandes de service
- Formulaire de demande personnalisé par catégorie
- Système de devis automatique et personnalisé
- Interface de suivi des demandes

### PHASE 4 - SYSTÈME DE RÉSERVATIONS (3-4 semaines)

#### Workflow de réservation
- États : demande → devis → acceptation → réalisation → paiement → évaluation
- Gestion des créneaux et disponibilités
- Système d'annulation avec politique de remboursement
- Notifications automatiques à chaque étape

#### Messagerie intégrée
- Chat temps réel entre client et prestataire
- Envoi de photos/documents
- Historique des conversations
- Notifications push

#### Gestion des interventions
- Check-in/check-out pour prestataires
- Rapport d'intervention avec photos
- Validation client de la prestation
- Gestion des litiges

### PHASE 5 - PAIEMENTS (2-3 semaines)

#### Intégration financière
- Laravel Cashier + Stripe pour paiements sécurisés
- Système de commissions plateforme (%)
- Portefeuille virtuel prestataires
- Facturation automatique

#### Gestion financière
- Paiements échelonnés pour gros montants
- Système de caution/garantie
- Remboursements automatiques
- Reporting financier

### PHASE 6 - SYSTÈME D'AVIS (1-2 semaines)

#### Évaluations bidirectionnelles
- Avis clients sur prestataires
- Avis prestataires sur clients
- Système de notes détaillées par critère
- Modération automatique et manuelle

#### Système de confiance
- Badges et certifications
- Score de fiabilité
- Vérification d'identité
- Assurance qualité

### PHASE 7 - ADMINISTRATION (2 semaines)

#### Dashboard admin
- Statistiques globales de la plateforme
- Gestion des utilisateurs et modération
- Validation des prestataires
- Gestion des catégories et services

#### Outils de support
- Interface de gestion des litiges
- Support client intégré
- Système de tickets
- Outils de communication de masse

### PHASE 8 - FONCTIONNALITÉS AVANCÉES (3-4 semaines)

#### Optimisations techniques
- Cache Redis pour performances
- Indexation ElasticSearch pour recherche
- API mobile responsive
- PWA pour installation mobile

#### Intelligence artificielle
- Algorithme de recommandation
- Pricing dynamique
- Détection de fraude
- Chatbot support client

### Estimation totale : 16-22 semaines

#### Technologies utilisées
- **Backend** : Laravel 12, MySQL, Redis, Stripe
- **Frontend** : Vue 3, TypeScript, Inertia.js, Tailwind CSS v4
- **Outils** : Pest (tests), Pint (formatting), Wayfinder (routing)
- **Services** : Stripe, Google Maps, SendGrid, Pusher

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.11
- inertiajs/inertia-laravel (INERTIA) - v2
- laravel/cashier (CASHIER) - v16
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/wayfinder (WAYFINDER) - v0
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/vue3 (INERTIA) - v2
- tailwindcss (TAILWINDCSS) - v4
- vue (VUE) - v3
- @laravel/vite-plugin-wayfinder (WAYFINDER) - v0
- eslint (ESLINT) - v9
- prettier (PRETTIER) - v3


## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== inertia-laravel/core rules ===

## Inertia Core

- Inertia.js components should be placed in the `resources/js/Pages` directory unless specified differently in the JS bundler (vite.config.js).
- Use `Inertia::render()` for server-side routing instead of traditional Blade views.
- Use `search-docs` for accurate guidance on all things Inertia.

<code-snippet lang="php" name="Inertia::render Example">
// routes/web.php example
Route::get('/users', function () {
    return Inertia::render('Users/Index', [
        'users' => User::all()
    ]);
});
</code-snippet>


=== inertia-laravel/v2 rules ===

## Inertia v2

- Make use of all Inertia features from v1 & v2. Check the documentation before making any changes to ensure we are taking the correct approach.

### Inertia v2 New Features
- Polling
- Prefetching
- Deferred props
- Infinite scrolling using merging props and `WhenVisible`
- Lazy loading data on scroll

### Deferred Props & Empty States
- When using deferred props on the frontend, you should add a nice empty state with pulsing / animated skeleton.

### Inertia Form General Guidance
- The recommended way to build forms when using Inertia is with the `<Form>` component - a useful example is below. Use `search-docs` with a query of `form component` for guidance.
- Forms can also be built using the `useForm` helper for more programmatic control, or to follow existing conventions. Use `search-docs` with a query of `useForm helper` for guidance.
- `resetOnError`, `resetOnSuccess`, and `setDefaultsOnSuccess` are available on the `<Form>` component. Use `search-docs` with a query of 'form component resetting' for guidance.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] <name>` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== pest/core rules ===

## Pest

### Testing
- If you need to verify a feature is working, write or update a Unit / Feature test.

### Pest Tests
- All tests must be written using Pest. Use `php artisan make:test --pest <name>`.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files - these are core to the application.
- Tests should test all of the happy paths, failure paths, and weird paths.
- Tests live in the `tests/Feature` and `tests/Unit` directories.
- Pest tests look and behave like this:
<code-snippet name="Basic Pest Test Example" lang="php">
it('is true', function () {
    expect(true)->toBeTrue();
});
</code-snippet>

### Running Tests
- Run the minimal number of tests using an appropriate filter before finalizing code edits.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).
- When the tests relating to your changes are passing, ask the user if they would like to run the entire test suite to ensure everything is still passing.

### Pest Assertions
- When asserting status codes on a response, use the specific method like `assertForbidden` and `assertNotFound` instead of using `assertStatus(403)` or similar, e.g.:
<code-snippet name="Pest Example Asserting postJson Response" lang="php">
it('returns all', function () {
    $response = $this->postJson('/api/docs', []);

    $response->assertSuccessful();
});
</code-snippet>

### Mocking
- Mocking can be very helpful when appropriate.
- When mocking, you can use the `Pest\Laravel\mock` Pest function, but always import it via `use function Pest\Laravel\mock;` before using it. Alternatively, you can use `$this->mock()` if existing tests do.
- You can also create partial mocks using the same import or self method.

### Datasets
- Use datasets in Pest to simplify tests which have a lot of duplicated data. This is often the case when testing validation rules, so consider going with this solution when writing tests for validation rules.

<code-snippet name="Pest Dataset Example" lang="php">
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
</code-snippet>


=== pest/v4 rules ===

## Pest 4

- Pest v4 is a huge upgrade to Pest and offers: browser testing, smoke testing, visual regression testing, test sharding, and faster type coverage.
- Browser testing is incredibly powerful and useful for this project.
- Browser tests should live in `tests/Browser/`.
- Use the `search-docs` tool for detailed guidance on utilizing these features.

### Browser Testing
- You can use Laravel features like `Event::fake()`, `assertAuthenticated()`, and model factories within Pest v4 browser tests, as well as `RefreshDatabase` (when needed) to ensure a clean state for each test.
- Interact with the page (click, type, scroll, select, submit, drag-and-drop, touch gestures, etc.) when appropriate to complete the test.
- If requested, test on multiple browsers (Chrome, Firefox, Safari).
- If requested, test on different devices and viewports (like iPhone 14 Pro, tablets, or custom breakpoints).
- Switch color schemes (light/dark mode) when appropriate.
- Take screenshots or pause tests for debugging when appropriate.

### Example Tests

<code-snippet name="Pest Browser Test Example" lang="php">
it('may reset the password', function () {
    Notification::fake();

    $this->actingAs(User::factory()->create());

    $page = visit('/sign-in'); // Visit on a real browser...

    $page->assertSee('Sign In')
        ->assertNoJavascriptErrors() // or ->assertNoConsoleLogs()
        ->click('Forgot Password?')
        ->fill('email', 'nuno@laravel.com')
        ->click('Send Reset Link')
        ->assertSee('We have emailed your password reset link!')

    Notification::assertSent(ResetPassword::class);
});
</code-snippet>

<code-snippet name="Pest Smoke Testing Example" lang="php">
$pages = visit(['/', '/about', '/contact']);

$pages->assertNoJavascriptErrors()->assertNoConsoleLogs();
</code-snippet>


=== inertia-vue/core rules ===

## Inertia + Vue

- Vue components must have a single root element.
- Use `router.visit()` or `<Link>` for navigation instead of traditional links.

<code-snippet name="Inertia Client Navigation" lang="vue">

    import { Link } from '@inertiajs/vue3'
    <Link href="/">Home</Link>

</code-snippet>


=== inertia-vue/v2/forms rules ===

## Inertia + Vue Forms

<code-snippet name="`<Form>` Component Example" lang="vue">

<Form
    action="/users"
    method="post"
    #default="{
        errors,
        hasErrors,
        processing,
        progress,
        wasSuccessful,
        recentlySuccessful,
        setError,
        clearErrors,
        resetAndClearErrors,
        defaults,
        isDirty,
        reset,
        submit,
  }"
>
    <input type="text" name="name" />

    <div v-if="errors.name">
        {{ errors.name }}
    </div>

    <button type="submit" :disabled="processing">
        {{ processing ? 'Creating...' : 'Create User' }}
    </button>

    <div v-if="wasSuccessful">User created successfully!</div>
</Form>

</code-snippet>


=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

    <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
        <div class="flex gap-8">
            <div>Superior</div>
            <div>Michigan</div>
            <div>Erie</div>
        </div>
    </code-snippet>


### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v4 rules ===

## Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff">
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>


### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option - use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |


=== tests rules ===

## Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test` with a specific filename or filter.
</laravel-boost-guidelines>
