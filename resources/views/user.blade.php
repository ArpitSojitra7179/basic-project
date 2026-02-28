<div>
@foreach ($paginator as $item)
<p>{{ $item['name'] }}</p>
@endforeach

{{ $paginator->links() }}
</div>
