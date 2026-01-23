<x-guest-layout>
    <div class="bg-white w-full max-w-lg mx-auto p-8 sm:p-10 rounded-[2.5rem] shadow-2xl relative">
        
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Buat LPJ Baru</h2>
            <p class="text-sm text-gray-400 mt-2 font-medium">Mulai laporan pertanggungjawaban anda di sini</p>
        </div>
        
        <form action="{{ route('lpj.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6 w-full
                        [&_input]:w-full
                        [&_input]:py-4
                        [&_input]:px-5
                        [&_input]:bg-gray-50 
                        [&_input]:rounded-2xl
                        [&_input]:border-gray-200 
                        [&_input]:text-gray-700 
                        [&_input]:placeholder-gray-400 
                        [&_input]:transition-all
                        [&_input]:shadow-sm
                        
                        [&_input:focus]:ring-4
                        [&_input:focus]:ring-[#6B81A7]/20
                        [&_input:focus]:border-[#6B81A7]
                        [&_input:focus]:bg-white
                        [&_input:focus]:outline-none

                        [&_label]:block
                        [&_label]:text-gray-600 
                        [&_label]:text-sm
                        [&_label]:font-bold
                        [&_label]:mb-2
                        [&_label]:ml-1
                        [&_label]:tracking-wide">

                <div>
                    <label for="title">Judul Dokumen</label>
                    <input type="text" name="title" id="title" 
                           placeholder="Contoh: Perjalanan Dinas ke Solo" 
                           required autofocus>
                </div>
                <div>
                    <label for="creator_name">Atas Nama (Penanggung Jawab)</label>
                    <input type="text" name="creator_name" id="creator_name" 
                           placeholder="Contoh: Ahmad Fulan, S.Kom" 
                           required>
                </div>

            </div>

            <div class="mt-8 space-y-3">
                <button type="submit" 
                        class="w-full py-4 text-lg font-bold text-white bg-[#4A5D7B] rounded-2xl 
                               hover:bg-[#36445a] hover:shadow-lg hover:-translate-y-0.5
                               transition-all duration-200 shadow-md active:scale-[0.98]">
                    Buat
                </button>
                </div>

        </form>
    </div>

</x-guest-layout>