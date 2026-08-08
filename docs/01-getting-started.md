# Başlangıç (Getting Started)

Palet Framework'e hoş geldiniz! Palet, PHP 8.3+ standartlarına tam uyumlu, sıkı tipli (strict types) ve Kurumsal (Enterprise) özellikleri barındıran modern bir web framework'üdür.

## Kurulum (Installation)

Palet Framework'ü kullanmaya başlamak için iki farklı yöntem kullanabilirsiniz: Palet CLI (Yükleyici) veya geleneksel Composer yöntemi.

### Yöntem 1: Palet CLI ile Kurulum (Önerilen)
Sisteminizde global olarak kurulu olan Palet Installer sayesinde saniyeler içinde yeni bir proje başlatabilirsiniz.

```bash
palet new benim-projem
```

Bu komut:
1. En güncel proje iskeletini indirir.
2. Bağımlılıkları (Composer) otomatik yükler.
3. `.env` dosyasını yapılandırır ve veritabanı ayarlarınızı hazır hale getirir.
4. Uygulama anahtarını (APP_KEY) oluşturur.

### Yöntem 2: Composer ile Kurulum
Alternatif olarak standart Composer aracını kullanarak projeyi oluşturabilirsiniz:

```bash
composer create-project omasat/palet-skeleton benim-projem dev-main
```

## Dizin Yapısı (Directory Structure)

Uygulamanız kurulduğunda aşağıdaki modern klasör yapısıyla karşılaşırsınız:

- `app/`: Uygulamanızın kalbidir. Controller'lar, Modeller, Middleware'ler ve Servisler buradadır.
- `bootstrap/`: Framework'ün ayağa kalktığı (boot) ve temel Container yapılandırmasının yapıldığı yerdir.
- `config/`: Uygulamanızın ayar dosyalarını (veritabanı, session, auth vb.) barındırır.
- `database/`: Veritabanı göçleri (migrations) ve sahte veri üreticiler (seeders) buradadır.
- `public/`: Web sunucunuzun (Nginx, Apache) kök dizinidir. Bütün istekler `index.php`'den geçer.
- `resources/`: View dosyalarınız (HTML/PHP), raw CSS (Tailwind) ve JS dosyalarınız buradadır.
- `routes/`: Web ve API yönlendirmelerinizin (routes) tanımlandığı klasördür.
- `storage/`: Loglar, derlenmiş View dosyaları ve Framework'ün geçici (cache) dosyaları burada tutulur.

## Geliştirme Sunucusunu Başlatmak

Kurulum tamamlandıktan sonra proje klasörünüze girin ve geliştirme sunucusunu ayağa kaldırın:

```bash
cd benim-projem
php palet serve
```

Tarayıcınızda `http://127.0.0.1:8000` adresine gittiğinizde Palet Framework'ün modern Dashboard'unu göreceksiniz.
