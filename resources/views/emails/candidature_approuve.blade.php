@component('mail::message')
# 🎉 Félicitations {{ $candidate->prenom }} {{ $candidate->nom }} !

Nous avons le plaisir de vous informer que votre candidature à l'élection **Reine ESGIS {{ date('Y') }}** a été **approuvée** ! ✨

Vous pouvez maintenant accéder à votre espace personnel pour :
- 📸 Gérer vos photos et vidéos
- 📊 Suivre vos votes en temps réel
- ✏️ Modifier vos informations
- 🏆 Voir votre classement

@component('mail::button', ['url' => url('/connexion')])
🔐 Se connecter à mon espace
@endcomponent

**Vos identifiants de connexion :**
- Email : {{ $candidate->email }}
- Mot de passe : Celui que vous avez choisi lors de l'inscription

---

💡 **Astuce :** Pensez à ajouter des photos de qualité et une vidéo de présentation pour maximiser vos chances !

Bonne chance pour le concours ! 🌟

Cordialement,
**L'équipe Reine ESGIS-Bénin**
@endcomponent
