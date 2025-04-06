<div class="m-5 mx-auto text-center">
    <h1 class="text-4xl mx-auto dark:text-white text-center p-5">Senelik Hicrî Kamerî Tarihler</h1>

    <form wire:submit="search">

        <div class="grid grid-rows-4 sm:grid-rows-3">

            <div>
                Hicri Yıl:
                <input type="text" wire:model="kameri_yil" class="border-2 rounded p-2">
            </div>

            <div>
                Miladi Yıl:
                <input type="text" wire:model="miladi_yil" class="border-2 rounded p-2 ">
            </div>
            
            <div>
                <button type="submit" class="dark:bg-white dark:text-black rounded text-white m-2 p-2">Ara</button>
            </div>

        </div>

       

        
    
        @if(count($result))


        <table class="mx-auto dark:text-white text-center hover:border-collapse mt-5 min-w-full">
            <tr class="text-xl">
                <td>Hicrî Kamerî Tarih</td>
                <td> Hicri Sene</td>
                <td> Miladi Sene</td>
                <td>Mubârek Günler</td>
                <td>GÜN</td>
                <td>Miladi</td>
            </tr>

            @foreach($result as $row)
            <tr>
                <td class="p-1 border border-td text-left">
                    {{$row->kameri_gun}}
                </td>
                <td class="p-1 border border-td text-left">
                    {{$row->kameri_yil}}
                </td>
                <td class="p-1 border border-td text-left">
                    {{$row->miladi_yil}}
                </td>
                <td class="p-1 border border-td text-left">
                    {{$row->mubarek_gun}}
                </td>
                <td class="p-1 border border-td text-left">
                    {{$row->gun}}
                </td>             
                <td class="p-1 border border-td text-left">
                    {{\Carbon\Carbon::parse($row->miladi)->format("d-m-Y")}}
                </td>                
            </tr> 
            @endforeach

        </table>    
        @else

            <p>Sonuç bulunamadı</p>

        @endif
    </form>
</div>
