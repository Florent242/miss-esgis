@component('mail::message')
# Bonjour {{ $candidate->prenom }} {{ $candidate->nom }},

Nous vous remercions sincèrement pour votre intérêt et votre candidature à l'élection **Miss ESGIS {{ date('Y') }}**.

Malheureusement, après examen de votre dossier, nous ne pouvons pas donner suite à votre candidature cette année.

Cette décision ne remet pas en cause vos qualités personnelles. Le nombre de places étant limité, nous avons dû faire des choix difficiles.

---

**Si vous pensez qu'il s'agit d'une erreur**, n'hésitez pas à contacter le comité d'organisation :
- 📧 Email : {{ env('MAIL_FROM_ADDRESS', 'contact@missesgis.com') }}
- 📱 En vous rapprochant directement de l'équipe

Nous vous encourageons à retenter votre chance l'année prochaine ! 💪

Cordialement,  
**L'équipe Miss ESGIS-Bénin**
@endcomponent
