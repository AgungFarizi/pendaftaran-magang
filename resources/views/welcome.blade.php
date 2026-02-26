<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tellinter - Sistem Pendaftaran Magang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full z-50" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold">
                        <span class="text-blue-600">Tell</span><span class="text-orange-500">inter</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-600 hover:text-blue-600">Beranda</a>
                    <a href="#about" class="text-gray-600 hover:text-blue-600">Tentang</a>
                    <a href="#programs" class="text-gray-600 hover:text-blue-600">Program</a>
                    <a href="#requirements" class="text-gray-600 hover:text-blue-600">Persyaratan</a>
                    <a href="#flow" class="text-gray-600 hover:text-blue-600">Alur Pendaftaran</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Daftar Sekarang</a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenu = !mobileMenu" class="text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenu" x-cloak class="md:hidden bg-white border-t">
            <div class="px-4 py-4 space-y-2">
                <a href="#home" class="block py-2 text-gray-600">Beranda</a>
                <a href="#about" class="block py-2 text-gray-600">Tentang</a>
                <a href="#programs" class="block py-2 text-gray-600">Program</a>
                <a href="#requirements" class="block py-2 text-gray-600">Persyaratan</a>
                <a href="#flow" class="block py-2 text-gray-600">Alur Pendaftaran</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block py-2 text-blue-600">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="block py-2 text-blue-600">Login</a>
                    <a href="{{ route('register') }}" class="block py-2 bg-blue-600 text-white text-center rounded-lg">Daftar Sekarang</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="pt-24 pb-16 bg-gradient-to-br from-blue-600 to-blue-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">
                        Mulai Karir Profesional Anda Bersama <span class="text-orange-400">Tellinter</span>
                    </h1>
                    <p class="text-xl text-blue-100 mb-8">
                        Program magang terbaik untuk mahasiswa yang ingin mengembangkan skill dan pengalaman kerja nyata di industri.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('register') }}" class="bg-orange-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-orange-600 text-center">
                            Daftar Magang
                        </a>
                        <a href="#programs" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 text-center">
                            Lihat Program
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="Internship" class="w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-600">500+</div>
                    <div class="text-gray-600">Alumni Magang</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-600">50+</div>
                    <div class="text-gray-600">Universitas Partner</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-600">10+</div>
                    <div class="text-gray-600">Departemen</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-600">95%</div>
                    <div class="text-gray-600">Tingkat Kepuasan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- About -->
    <section id="about" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Tentang Program Magang</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Tellinter menyediakan program magang yang dirancang untuk mempersiapkan mahasiswa menghadapi dunia kerja profesional.
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Pengalaman Nyata</h3>
                    <p class="text-gray-600">Bekerja pada proyek nyata bersama tim profesional dan dapatkan pengalaman industri yang berharga.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Mentoring Intensif</h3>
                    <p class="text-gray-600">Dibimbing langsung oleh pembimbing lapang yang berpengalaman di bidangnya.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Sertifikat Resmi</h3>
                    <p class="text-gray-600">Dapatkan sertifikat magang resmi yang dapat meningkatkan nilai CV Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs -->
    <section id="programs" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Program Magang Tersedia</h2>
                <p class="text-gray-600">Pilih program magang sesuai dengan minat dan jurusan Anda</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
    $periods = \App\Models\InternshipPeriod::registrationOpen()
        ->take(6)
        ->get();
@endphp

@forelse($periods as $period)
<div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-blue-300 transition">
    <div class="flex justify-between items-start mb-4">
        <span class="bg-green-100 text-green-700 text-sm px-3 py-1 rounded-full">
            {{ $period->isRegistrationOpen() ? 'Dibuka' : 'Ditutup' }}
        </span>
        <span class="text-gray-500 text-sm">
            Kuota: {{ $period->quota }}
        </span>
    </div>

    <h3 class="text-lg font-semibold text-gray-800 mb-2">
        {{ $period->title }}
    </h3>

    <p class="text-gray-600 text-sm mb-4">
        {{ \Illuminate\Support\Str::limit($period->description, 100) }}
    </p>

    <div class="text-sm text-gray-500 mb-4">
        <div class="flex items-center mb-1">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>

                    {{ $period->start_registration->format('d M Y') }}
                    -
                    {{ $period->end_registration->format('d M Y') }}
                </div>
            </div>

            <a href="{{ route('register') }}"
            class="block text-center bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                Daftar Sekarang
            </a>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <p class="text-gray-500">
                Belum ada program magang yang dibuka saat ini.
            </p>
        </div>
        @endforelse
            </div>
        </div>
    </section>

    <!-- Requirements -->
    <section id="requirements" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Persyaratan Pendaftaran</h2>
                <p class="text-gray-600">Pastikan Anda memenuhi persyaratan berikut sebelum mendaftar</p>
            </div>
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                        Persyaratan Umum
                    </h3>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Mahasiswa aktif minimal semester 5
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            IPK minimal 3.00
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Bersedia magang selama 3-6 bulan
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Memiliki kemampuan komunikasi yang baik
                        </li>
                    </ul>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3 text-sm">2</span>
                        Dokumen yang Diperlukan
                    </h3>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Surat pengantar dari universitas
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Proposal magang (PDF)
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            CV/Resume terbaru
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Transkrip nilai sementara
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Flow -->
    <section id="flow" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Alur Pendaftaran</h2>
                <p class="text-gray-600">Ikuti langkah-langkah berikut untuk mendaftar magang</p>
            </div>
            <div class="grid md:grid-cols-5 gap-4">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">1</div>
                    <h3 class="font-semibold text-gray-800 mb-2">Daftar Akun</h3>
                    <p class="text-sm text-gray-600">Buat akun dengan email mahasiswa Anda</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">2</div>
                    <h3 class="font-semibold text-gray-800 mb-2">Ajukan Proposal</h3>
                    <p class="text-sm text-gray-600">Upload proposal dan tambahkan anggota tim</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">3</div>
                    <h3 class="font-semibold text-gray-800 mb-2">Review & Approval</h3>
                    <p class="text-sm text-gray-600">Proposal direview operator dan disetujui manager</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">4</div>
                    <h3 class="font-semibold text-gray-800 mb-2">Surat Balasan</h3>
                    <p class="text-sm text-gray-600">Terima surat balasan jika diterima</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">5</div>
                    <h3 class="font-semibold text-gray-800 mb-2">Mulai Magang</h3>
                    <p class="text-sm text-gray-600">Mulai magang dan isi log harian</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-blue-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Siap Memulai Perjalanan Karir Anda?</h2>
            <p class="text-blue-100 mb-8">Daftar sekarang dan jadilah bagian dari tim Tellinter!</p>
            <a href="{{ route('register') }}" class="inline-block bg-orange-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-orange-600">
                Daftar Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-2xl font-bold text-white mb-4">
                        <span class="text-blue-400">Tell</span><span class="text-orange-400">inter</span>
                    </h3>
                    <p class="text-sm">Sistem pendaftaran magang terbaik untuk mahasiswa Indonesia.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#home" class="hover:text-white">Beranda</a></li>
                        <li><a href="#about" class="hover:text-white">Tentang</a></li>
                        <li><a href="#programs" class="hover:text-white">Program</a></li>
                        <li><a href="#requirements" class="hover:text-white">Persyaratan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm">
                        <li>Email: info@tellinter.com</li>
                        <li>Phone: (021) 1234-5678</li>
                        <li>Alamat: Jakarta, Indonesia</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Ikuti Kami</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-white">Facebook</a>
                        <a href="#" class="hover:text-white">Instagram</a>
                        <a href="#" class="hover:text-white">LinkedIn</a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} Tellinter. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
