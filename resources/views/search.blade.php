<h2>Rezultati za: "{{ $search }}"</h2>

@if(is_array($products) && count($products) > 0)
    <table>
        <thead>
            <tr>
                @foreach((array) $products[0] as $key => $val)
                    <th>{{ $key }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    @foreach((array) $product as $val)
                        <td>{{ $val }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>Nema rezultata.</p>
@endif
