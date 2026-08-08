# Yönlendirme ve Controller Sistemi (Routing & Controllers)

Palet Framework'ün Yönlendiricisi (Router) son derece hızlı ve güvenli bir yapıya sahiptir. Tarayıcıdan gelen her HTTP isteği, öncelikle `public/index.php` üzerinden `Router` nesnesine ulaşır.

## Rota Tanımlama (Defining Routes)

Tüm rotalarınız uygulamanızdaki `routes/web.php` dosyası içerisinde tanımlanır. Rotalar doğrudan bir Anonim Fonksiyon (Closure) veya bir Controller sınıfına yönlendirilebilir.

### Basit Rota Tanımlamaları

```php
$router->get('/merhaba', function() {
    return 'Merhaba Palet!';
});

// Dinamik Parametreli Rotalar
$router->get('/kullanici/{id}', function($request, $routeMatch) {
    $id = $routeMatch->getParam('id');
    return "Kullanıcı ID: " . $id;
});
```

### Controller Yönlendirmeleri (Önerilen)

Kurumsal projelerde mantığı Controller sınıflarına ayırmak en doğrusudur. Palet Framework, Array (Dizi) sözdizimini (syntax) destekler.

```php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;

$router->get('/', [HomeController::class, 'index']);
$router->post('/login', [AuthController::class, 'authenticate']);
```

Dikkat edin: Bir rotaya `POST` ile veri gönderiyorsanız `$router->post()` metodunu kullanmalısınız. Yanlış metoda atılan istekler güvenlik gereği `405 Method Not Allowed` hatası fırlatır.

## Controller Yapısı

Palet Framework'te Controller sınıfları otomatik olarak Service Container (IoC) tarafından çözümlenir (resolve). Bu sayede sınıfların kurucu metotlarına (Constructor) veya doğrudan hedef metoda bağımlılıklarınızı (Dependency) yazabilirsiniz. Framework bunları otomatik olarak enjekte eder (Dependency Injection).

```php
namespace App\Http\Controllers;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use App\Services\UserService;

class AuthController
{
    protected UserService $userService;

    // IoC Container, UserService'i otomatik olarak bulur ve enjekte eder!
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // IoC Container, o anki HTTP İsteğini (RequestInterface) metoda otomatik gönderir!
    public function authenticate(RequestInterface $request)
    {
        $credentials = [
            'email' => $request->getParsedBody()['email'] ?? '',
            'password' => $request->getParsedBody()['password'] ?? '',
        ];

        if (auth()->attempt($credentials)) {
            return redirect('/dashboard');
        }

        return redirect('/login?error=1');
    }
}
```

## Geri Dönüş Tipleri (Responses)

Bir Controller metodundan dönen her şey, Framework'ün `ActionResultNormalizer` sınıfı sayesinde otomatik olarak doğru HTTP Yanıtına (Response) dönüştürülür:

- **String:** Metin döndürdüğünüzde otomatik olarak HTML (200 OK) sayfası oluşur.
- **Array/Object:** Bir dizi döndürdüğünüzde Framework onu güvenli bir şekilde JSON formatına çevirip `application/json` başlığıyla tarayıcıya gönderir (Özellikle API yaparken harikadır).
- **View Nesnesi:** `return view('dashboard');` dediğinizde HTML şablonunuz derlenip ekrana basılır.
