@extends('layouts.base')
@php
    $titre = 'Dashboard admin - Miss ESGIS ' . date('Y');

@endphp
@vite('resources/css/styleadmin.css')
@section('title', $titre)

@section('content')
    <div class="content">
        <section class="entete">
            <h1>Interface Administrateur</h1>
            <p>Gestion du concours Miss ESGIS {{ date('Y') }}</p>
        </section>

        <section class="statistique">
            <div id="bloc">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-users w-8 h-8 text-primary">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="chiffre">
                    @if (count($candidates) === 0)
                        :
                        <p>Aucune Candidate inscrite cette année</p>
                    @else
                        <h3>{{ count($candidates) }}</h3>
                        <p>Candidates totales</p>
                    @endif
                </div>
            </div>
            <div id="bloc">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-vote w-8 h-8 text-primary">
                        <path d="m9 12 2 2 4-4"></path>
                        <path d="M5 7c0-1.1.9-2 2-2h10a2 2 0 0 1 2 2v12H5V7Z"></path>
                        <path d="M22 19H2"></path>
                    </svg>
                </div>
                <div class="chiffre">
                    @if (count($candidates) === 0)
                        :
                        <p>Aucune Candidate inscrite cette année</p>
                    @else
                        <h3>{{ $candidates->sum('votes_count') }}</h3>
                        <p>Votes totaux</p>
                    @endif
                </div>
            </div>
            <div id="bloc">
                <div class="w-8 h-8 bg-secondary rounded-lg flex items-center justify-center">FCFA</div>
                <div class="chiffre">
                    @if (count($candidates) === 0)
                        :
                        <p>Aucune Candidate inscrite cette année</p>
                    @else
                        <h3>{{ $transactions->sum('montant') }}</h3>
                        <p>Revenus</p>
                    @endif
                </div>
            </div>
            <div id="bloc">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-user w-8 h-8 text-orange-500">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div class="chiffre">
                    @if (count($candidates) === 0)
                        :
                        <p>Aucune Candidate inscrite cette année</p>
                    @else
                        <h3>{{ count($candidates->where('statut', 'pending')) }}</h3>
                        <p>En attente</p>
                    @endif
                </div>
            </div>
        </section>

        <section class="detail">
            <nav>
                <span class="active" id="Candidates">Candidates</span>
                <span id="candidatesaprouver">Candidates acceptées</span>
                <span id="Classement">Classement</span>
                <span id="Transactions">Transactions</span>
            </nav>
            @if (session('success'))
                <div id="showtoast"
                    style="background-color: #5aeb17ff; color: white; padding: 15px; border-radius: 5px; margin: 10px 0;">
                    {{ session('success') }}</div>
                <script>
                    setTimeout(() => {
                        if (document.getElementById('showtoast')) {
                            document.getElementById('showtoast').style.opacity = '0'
                            setTimeout(() => {
                                document.getElementById('showtoast')?.remove()
                            }, 500)
                        }
                    }, 3000)
                </script>
            @endif
            <div id="tabStatistique">

            </div>
        </section>
    </div>
    <script>
        const Candidates = document.getElementById("Candidates")
        const Classement = document.getElementById("Classement")
        const Transactions = document.getElementById("Transactions")
        const candidatesaprouver = document.getElementById("candidatesaprouver")
        const tabStatistique = document.getElementById("tabStatistique")
        const candidatesapp = @json($candidatesaprouver);
        const candidates = @json($candidates);
        if (candidates.length == 0) {
            tabStatistique.innerHTML = `<h1>Gestion des candidates</h1>
            <div>Aucune candidate inscrite</div>
        `
        } else {
            tabStatistique.innerHTML = desinertabCandidat(candidates)
        }
        Classement.addEventListener("click", function() {
            Candidates.className = ""
            Classement.className = "active"
            Transactions.className = ""
            candidatesaprouver.className = ""
            const candidates = @json($candidates);

            if (candidates.length == 0) {
                tabStatistique.innerHTML = `<h1>Classement des candidates</h1>
            <div>Aucune candidate inscrite</div>
        `
            } else {

                let entete = `

        <h1>Classement des candidates</h1>
        <div class="candidate" id="premiere">
                <div class="presentation">
                    <p class="position">1</p>
                    <div>
                        <p class="nom">${candidates[0].nom +" "+candidates[0].prenom}</p>
                        <p class="ville">${candidates[0].pays}</p>
                    </div>
                </div>
                <div class="votes">
                    <p>${candidates[0].votes_count}</p>
                    <p>votes</p>
                </div>
                <div class="mention">
                    En tete
                </div>
        </div>`
                let contenu = "";
                for (let i = 1; i < candidates.length; i++)
                    contenu += `<div class="candidate">
                <div class="presentation">
                    <p class="position">${i+1}</p>
                    <div>
                        <p class="nom">${candidates[i].nom +" "+candidates[i].prenom}</p>
                        <p class="ville">${candidates[i].pays}</p>
                    </div>
                </div>

                <div class="votes">
                     <p>${candidates[i].votes_count}</p>
                    <p>votes</p>
                </div>
        </div>`
                tabStatistique.innerHTML = entete + contenu
            }
        })


        Candidates.addEventListener("click", function() {
            Candidates.className = "active"
            Classement.className = ""
            Transactions.className = ""
            candidatesaprouver.className = ""
            if (candidates.length == 0) {
                tabStatistique.innerHTML = `<h1>Gestion des candidates</h1>
            <div>Aucune candidate inscrite</div>
        `
            } else {
                tabStatistique.innerHTML = desinertabCandidat(candidates)
            }
        })

        candidatesaprouver.addEventListener("click", function() {
            Candidates.className = ""
            Classement.className = ""
            Transactions.className = ""
            candidatesaprouver.className = "active"

            if (candidatesapp.length == 0) {
                tabStatistique.innerHTML = `<h1>Gestion des candidates</h1>
            <div>Aucune candidate validé</div>
        `
            } else {
                tabStatistique.innerHTML = desinertabCandidat(candidatesapp)
            }
        })

        Transactions.addEventListener("click", function() {
            Candidates.className = ""
            Classement.className = ""
            Transactions.className = "active"
            const transactions = @json($transactions);
            if (transactions.length == 0) {
                tabStatistique.innerHTML = `<h1>Transactions</h1>
            <div>Aucune transaction effectué</div>
        `
            } else {

                const transactions = @json($transactions);
                let contenu = "";
                let i = 1;
                const tetetab = `

        <h1>Transactions</h1>
    <table>
        <thead>
            <tr>
                <th>ID Transaction</t>

                <th>Candidate</t>

                <th>Montant</t>

                <th>Méthode</t>

                <th>Date</t>
            </tr>
        </thead>
        <tbody>`;

                for (let transaction of transactions) {
                    contenu += `
            <tr ${i%2==0 ? 'id="paire"':''}>
                <td> <strong> ${transaction.id}</strong></td>
                <td>${transaction.miss.nom +" "+transaction.miss.prenom}</td>
                <td>${transaction.montant} Fcfa</td>
                <td>${transaction.methode}</td>
                <td>${transaction.date.split('T')[0]}</td>
            </tr>
            `
                    i++
                }
                const fintab = `</tbody>
    </table>
        `
                tabStatistique.innerHTML = tetetab + contenu + fintab
            }

        })


        function desinertabCandidat(listcandidate) {

            const candidates = listcandidate;
            let contenu = "";
            let i = 1;
            const tetetab = `

        <h1>Gestion des candidates</h1>
    <table>
        <thead>
            <tr>
                <th>Nom</t>

                <th>Ville</t>

                <th>Votes</t>

                <th>Statut</t>

                <th>Date inscription</t>

                <th>Actions</t>
            </tr>
        </thead>
        <tbody>`;

            for (let candidate of candidates) {

                let approuveroute = "{{ url('/approuve') }}/:id"
                let rejeteroute = "{{ url('/refuse') }}/:id"

                let status = ""
                if (candidate.statut == "pending") {
                    status = "En attente"
                }
                if (candidate.statut == "active") {
                    status = "Aprouvée"
                }
                if (candidate.statut == "reject") {
                    status = "Rejetée"
                }
                contenu += `
            <tr ${i%2==0 ? 'id="paire"':''}>
                <td> <strong> ${candidate.nom +" "+ candidate.prenom}</strong></td>
                <td>${candidate.pays}</td>
                <td>${candidate.votes_count}</td>
                <td id="approuvee">${status}</td>
                <td>${candidate.date_inscription.split('T')[0]}</td>
                <td>
                    <div class = "flex space-x-2">
                    <a href="${approuveroute.replace(':id',candidate.id)}"><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border bg-background hover:text-accent-foreground h-9 rounded-md px-3 text-green-600 border-green-600 hover:bg-green-50"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-4 h-4"><path d="M20 6 9 17l-5-5"></path></svg></button></a>
                    <a href="${rejeteroute.replace(':id',candidate.id)}"><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border bg-background hover:text-accent-foreground h-9 rounded-md px-3 text-red-600 border-red-600 hover:bg-red-50">✕</button></a>
                    </div>
                </td>
            </tr>
            `
                i++
            }
            const fintab = `</tbody>
    </table>
        `
            tab = tetetab + contenu + fintab
            return tab
        }
    </script>
@endsection
