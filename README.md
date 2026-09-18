# Site Izleme Paneli

Birden cok web sitesinin ayakta olma ve SSL durumunu izleyen Livewire panel.

## Ozellikler

- Periyodik erisim kontrolu ve yanit suresi olcumu
- SSL sertifika bitis takibi ve uyari
- Canli guncellenen durum sayfasi (Livewire)
- E-posta bildirimleri ve rapor ekranlari

## Kullanilan teknolojiler

Laravel 11 - PHP 8.2 - Livewire - Tailwind

## Bu depo hakkinda

Gercek bir musteri projesinin **portfolyo icin yayinlanmis** surumudur.
Yayina hazirlanirken canli alan adlari, gercek iletisim bilgileri, musteri
kayitlari ve uygulama anahtarlari ornek degerlerle degistirilmistir.
Kod ve mimari oldugu gibidir; veri gercek degildir.

## Kurulum

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
