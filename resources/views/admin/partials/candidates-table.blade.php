<div class="overflow-x-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Candidates en attente de validation</h2>
    @if(count($candidates) === 0)
        <div class="text-center py-12">
            <p class="text-gray-500">Aucune candidate en attente</p>
        </div>
    @else
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Candidate</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($candidates as $candidate)
                <tr>
                    <td class="px-6 py-4">{{ $candidate->prenom }} {{ $candidate->nom }}</td>
                    <td class="px-6 py-4">
                        <form action="{{ route('admin.approve', $candidate->id) }}" method="POST" class="inline">
                            @csrf
                            <button class="text-green-600">Approuver</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
