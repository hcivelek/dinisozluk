<div class="m-5 mx-auto text-center">
    <h1 class="text-4xl mx-auto text-green-800 text-center p-5">Dini Terimler Sözlüğü</h1>
    <form wire:submit="search">
        <input type="text" wire:model="keyword" class="border-2 rounded p-2 border-green-700/100">
    
        <button type="submit" class="bg-green-900 rounded text-white p-2">Ara</button>


        <table class="mx-auto text-center hover:border-collapse mt-5 min-w-full">

            @foreach($result as $row)
            <tr>
                <td class="p-1 font-bold cursor-pointer border border-green-600 text-left" wire:click="select('{{addslashes($row->word)}}', '{{$keyword}}')">
                    {{$row->word}}
                </td>
            </tr>
                @if($selected == $row->word)
                <tr>
                    <td class="p-1 border border-green-600 text-left">{!! nl2br($row->detail) !!}</td>
                </tr>
                @endif
            @endforeach

        </table>    
    </form>
    <button onclick="scrollToTop()" id="backToTopBtn" style="position: fixed; bottom: 20px; right: 20px; display: none;">
        <img src="/up.png" style="width: 45px;">
    </button>

    <script>
  // Sayfayı yumuşak bir şekilde en üste kaydıran fonksiyon
  function scrollToTop() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  }

  // Butonu sayfa kaydırıldığında göster/gizle
  window.onscroll = function() {
    const backToTopBtn = document.getElementById('backToTopBtn');
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
      backToTopBtn.style.display = "block";
    } else {
      backToTopBtn.style.display = "none";
    }
  };
</script>    
</div>
