# Kurumsal (Enterprise) Özellikler ve Eksik Yapıları Geliştirme

Palet Framework'ü rakiplerinden ayıran en büyük unsur, kurumsal seviyedeki yazılımlarda (SaaS, ERP, B2B vb.) ihtiyaç duyulan ileri seviye özelliklerin çekirdek yapıda (Core) planlanmış olmasıdır.

## 1. Tenancy (Çoklu Kiracı/Müşteri Sistemi)

Tenancy, tek bir veritabanı (veya çoklu veritabanı) kullanarak uygulamanızın birden fazla şirket (müşteri) tarafından birbirinden tamamen izole (bağımsız) şekilde kullanılmasını sağlar.

### Nasıl Çalışır?
Bir kullanıcı sisteme giriş yaptığında, sisteme ait `TenantManager` kullanıcının hangi organizasyona (Tenant) ait olduğunu belirler. O andan itibaren sistemdeki tüm veritabanı sorguları (Siparişler, Faturalar vb.) sadece o Tenant'ın ID'sine göre filtrelenir. Başka bir şirketin verilerini göremezsiniz.

Bu sayede devasa SaaS projelerini (Örnek: Bulut Muhasebe Yazılımları) tek bir framework üzerinde güvenle yürütebilirsiniz.

## 2. İş Akışı (Workflow)

Workflow modülü, onay süreçlerini (Örneğin: "Müdür izin onaylasın -> İK onaylasın -> Sistem mail atsın") kod blokları arasına sıkıştırmak yerine, tanımlanabilir ve izlenebilir durum makineleri (State Machine) olarak yönetmenizi sağlar.

- **Place (Durum):** Belgenin şu anki durumu (Örn: "Beklemede").
- **Transition (Geçiş):** Durum değiştiren eylem (Örn: "Onayla").

## Palet Framework'te "Eksik" Yapıları Geliştirmek

Palet Framework henüz v1.0 aşamasında olduğu için, bazı yardımcı bileşenleri (örneğin Redis önbellekleme, gelişmiş Kuyruk sistemleri) çekirdekte bulamayabilirsiniz. Framework, **Genişletilebilir (Extensible)** mimari üzerine kurulduğu için bu parçaları kendi projenize çok kolay ekleyebilirsiniz.

### Kendi Servis Sağlayıcınızı (Service Provider) Eklemek

Diyelim ki sisteme dışarıdan indirdiğiniz bir SMS gönderme paketini dahil etmek istiyorsunuz.
Framework'ün çekirdek dosyalarını (`vendor/omasat/palet-framework`) **KESİNLİKLE DÜZENLEMEMELİSİNİZ!** Her şeyi kendi uygulamanızın (`app/`) içinde yapmalısınız.

**Adım 1:** `app/Providers/SmsServiceProvider.php` oluşturun.

```php
namespace App\Providers;

use Palet\Framework\Support\ServiceProvider;
use App\Services\SmsService;

class SmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Container'a SmsService'i öğretiyoruz
        $this->app->singleton(SmsService::class, function () {
            return new SmsService('API_KEY_BURAYA');
        });
    }

    public function boot(): void
    {
        // Sistem tamamen ayaklandıktan sonra yapılacak işlemler...
    }
}
```

**Adım 2:** Bu provider'ı uygulamanızın ayağa kalktığı yere (Genelde `bootstrap/app.php` veya `config/app.php` içerisinde yer alan providers dizisine) ekleyin.

```php
// config/app.php
'providers' => [
    \Palet\Framework\View\ViewServiceProvider::class,
    \App\Providers\SmsServiceProvider::class, // Sizin kendi Provider'ınız!
],
```

Tebrikler! Artık uygulamanızdaki bütün Controller sınıflarına `__construct(SmsService $sms)` yazarak SMS gönderme motorunu otomatik dahil edebilirsiniz. Çekirdeği hiç bozmadan Framework'ü genişlettiniz!
