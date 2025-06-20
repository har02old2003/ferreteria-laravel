@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://ui-avatars.com/api/?name=Ferrechincha&color=7F9CF5&background=EBF4FF" class="logo" alt="Ferrechincha Logo" style="width: 100px; height: 100px; border-radius: 50%;">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
