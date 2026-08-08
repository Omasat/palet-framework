# IoC Container & Temel Mimari (Architecture)

Palet Framework'ün en güçlü özelliği olan IoC (Inversion of Control) Service Container, uygulamanızdaki tüm bağımlılıkları yöneten bir orkestra şefi gibidir.

## Service Container Nedir?

Container, basitçe "sınıfların kayıt defteri ve üreticisi"dir. Uygulamanızda bir sınıfa ihtiyaç duyduğunuzda onu manuel olarak `new UserService()` diye başlatmak yerine, Container'dan istersiniz. Container, o sınıfın ihtiyaç duyduğu diğer alt sınıfları (Dependency) bularak otomatik olarak iç içe yükler (Auto Wiring).

### Bağımlılık Enjeksiyonu (Dependency Injection)

Örneğin, Controller'ınız bir `EmailService`'e ihtiyaç duyuyorsa, bunu yapmanın en iyi yolu:

```php
namespace App\Http\Controllers;

use App\Services\EmailService;

class NotificationController
{
    // Container, EmailService'i kendi yaratır ve buraya gönderir.
    public function send(EmailService $email)
    {
        $email->sendTo('test@example.com', 'Merhaba!');
        return "Gönderildi.";
    }
}
```

## Arayüz (Interface) Bağlama (Binding)

Katı bağımlılıkları kırmak için (SOLID Prensipleri - Dependency Inversion) projenizde `Interface` (Arayüz) kullanmalısınız. Container'a bir Interface istendiğinde hangi somut (Concrete) sınıfın verileceğini söyleyebilirsiniz.

Genellikle bu işlemleri `AppServiceProvider` gibi Service Provider'lar (Servis Sağlayıcıları) içinde yaparız:

```php
// Bir Interface (Arayüz)
interface PaymentGatewayInterface {
    public function charge(int $amount);
}

// Somut Bir Sınıf (Concrete Class)
class StripeGateway implements PaymentGatewayInterface {
    public function charge(int $amount) {
        // Stripe API Kodları
    }
}

// Uygulama başlarken (Bootstrap) Container'a bağlama işlemi:
$app->bind(PaymentGatewayInterface::class, StripeGateway::class);
```

Artık sistemin neresinde `PaymentGatewayInterface` isterseniz, Palet Container size otomatik olarak `StripeGateway` sınıfını verecektir.

## Singleton Mimarisi
Bazen bir sınıfın uygulama yaşam döngüsü (Request Lifecycle) boyunca sadece bir kez üretilmesini ve her seferinde aynı objenin kullanılmasını istersiniz (Örneğin Veritabanı bağlantısı). Bunun için `bind` yerine `singleton` kullanılır:

```php
$app->singleton(DatabaseConnection::class, function() {
    return new DatabaseConnection('root', 'password');
});
```

Artık `DatabaseConnection` nerede istenirse istensin, her defasında yeni bir bağlantı açılmaz, en baştaki bağlantı nesnesi paylaşılır.
