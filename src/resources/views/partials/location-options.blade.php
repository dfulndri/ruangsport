{{-- Variabel: $locations (kota dengan children), $selected --}}
<option value="">Pilih lokasi</option>
@foreach ($locations as $city)
  <option value="{{ $city->id }}" @selected((string) $selected === (string) $city->id)>{{ $city->name }}</option>
  @foreach ($city->children->sortBy('name') as $district)
    <option value="{{ $district->id }}" @selected((string) $selected === (string) $district->id)>&nbsp;&nbsp;{{ $district->name }}</option>
  @endforeach
@endforeach
