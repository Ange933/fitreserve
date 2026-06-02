<template>
  <div class="overflow-x-hidden">

    <!-- HERO -->
    <section class="relative min-h-screen flex items-end">
      <div class="absolute inset-0 z-0">
        <img src="/images/hero.jpg" alt="Salle de sport de Marc Durand, coach sportif Paris" class="w-full h-full object-cover object-center" />
        <div class="absolute inset-0" style="background: linear-gradient(to right, rgba(0,0,0,0.9) 45%, rgba(0,0,0,0.2) 100%)"></div>
        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(15,15,15,1) 0%, transparent 55%)"></div>
      </div>

      <div class="relative z-10 max-w-7xl mx-auto px-4 w-full pb-24 pt-32">
        <div class="max-w-2xl">
          <p class="label-tag text-orange-500 mb-5">Coach Sportif Certifié — Paris 8e</p>
          <h1 class="font-display font-black uppercase leading-none mb-6" style="font-size: clamp(3rem, 8vw, 6.5rem)">
            <span class="text-white block">Marc</span>
            <span class="text-white block">Durand</span>
            <span class="text-gradient block">Coach.</span>
          </h1>
          <p class="text-zinc-300 text-lg leading-relaxed mb-10 max-w-lg font-medium">
            10 ans d'expérience. Des méthodes éprouvées. Un seul objectif : vous faire dépasser vos limites et atteindre vos résultats.
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            <router-link to="/seances" class="btn-primary text-sm py-4 px-8 w-full sm:w-auto text-center">
              Réserver une séance
            </router-link>
            <a href="#about" class="btn-ghost text-sm py-4 px-8 w-full sm:w-auto text-center">
              En savoir plus
            </a>
          </div>
        </div>

        <!-- Stats -->
        <div class="flex flex-wrap gap-10 mt-16 pt-8 border-t border-white/10">
          <div v-for="stat in stats" :key="stat.label">
            <p class="font-display font-black text-4xl text-orange-500">{{ stat.value }}</p>
            <p class="text-zinc-500 text-xs uppercase tracking-widest mt-1">{{ stat.label }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ABOUT -->
    <section id="about" class="py-24 px-4 bg-dark-950">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

          <div class="relative order-2 lg:order-1">
            <img
              src="/images/deadlift.jpg"
              alt="Marc Durand en séance d'entraînement"
              class="w-full h-[520px] object-cover"
              style="clip-path: polygon(0 0, calc(100% - 20px) 0, 100% 20px, 100% 100%, 20px 100%, 0 calc(100% - 20px))"
            />
            <!-- Badge certif -->
            <div class="absolute -bottom-4 -right-4 bg-dark-800 border border-orange-500 p-5 text-center" style="clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 10px, 100% 100%, 10px 100%, 0 calc(100% - 10px))">
              <p class="font-display font-black text-orange-500 text-3xl">BPJEPS</p>
              <p class="text-zinc-500 text-xs uppercase tracking-wider">Certifié d'État</p>
            </div>
          </div>

          <div class="order-1 lg:order-2">
            <p class="label-tag text-orange-500 mb-4">Votre coach</p>
            <h2 class="section-title text-white mb-6">
              Une approche<br /><span class="text-gradient">100% personnalisée.</span>
            </h2>
            <p class="text-zinc-400 leading-relaxed mb-6">
              Ancien athlète de haut niveau reconverti coach en 2014, Marc Durand a accompagné plus de 200 clients vers leurs objectifs — perte de poids, prise de masse, remise en forme, préparation sportive.
            </p>
            <p class="text-zinc-400 leading-relaxed mb-8">
              Chaque séance est conçue pour vous : votre niveau, votre corps, vos contraintes. Pas de programme générique, pas de résultat générique.
            </p>
            <div class="grid grid-cols-2 gap-3 mb-10">
              <div v-for="cert in certifications" :key="cert" class="flex items-center gap-2 text-sm text-zinc-300">
                <div class="w-1.5 h-1.5 bg-orange-500 flex-shrink-0"></div>
                {{ cert }}
              </div>
            </div>
            <router-link to="/register" class="btn-primary text-sm py-3 px-8 inline-flex">
              Commencer maintenant
            </router-link>
          </div>

        </div>
      </div>
    </section>

    <!-- COMMENT CA FONCTIONNE -->
    <section class="py-24 px-4 bg-dark-900">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <p class="label-tag text-orange-500 mb-4">Le principe</p>
          <h2 class="section-title text-white">
            Vous réservez.<br /><span class="text-gradient">Marc s'occupe du reste.</span>
          </h2>
          <p class="text-zinc-500 mt-6 max-w-xl mx-auto text-base leading-relaxed">
            Pas de programme imposé. Lors de chaque séance, Marc adapte l'entraînement à votre forme du jour, vos envies et vos objectifs. Vous n'avez qu'à vous présenter.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <div
            v-for="(point, i) in points"
            :key="point.title"
            class="bg-dark-800 border border-dark-600 p-8 relative"
            style="clip-path: polygon(0 0, calc(100% - 12px) 0, 100% 12px, 100% 100%, 12px 100%, 0 calc(100% - 12px))"
          >
            <span class="font-display font-black text-6xl text-dark-600 leading-none block mb-4">0{{ i + 1 }}</span>
            <h3 class="font-display font-black text-white uppercase text-xl mb-3">{{ point.title }}</h3>
            <p class="text-zinc-500 text-sm leading-relaxed">{{ point.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- PHOTO BANDE + CITATION -->
    <section class="relative h-72 md:h-96 overflow-hidden">
      <img src="/images/pullups.jpg" alt="Athlète en entraînement" class="w-full h-full object-cover object-top" />
      <div class="absolute inset-0 bg-dark-950/70 flex items-center justify-center px-4">
        <blockquote class="text-center max-w-3xl">
          <p class="font-display font-black text-white uppercase leading-tight" style="font-size: clamp(1.5rem, 4vw, 3rem)">
            "Le seul mauvais entraînement,<br />c'est celui que vous<br /><span class="text-orange-500">n'avez pas fait."</span>
          </p>
          <footer class="text-zinc-500 text-sm uppercase tracking-widest mt-4">— Marc Durand</footer>
        </blockquote>
      </div>
    </section>

    <!-- COMMENT CA MARCHE -->
    <section class="py-24 px-4 bg-dark-950">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <p class="label-tag text-orange-500 mb-4">Simple & rapide</p>
          <h2 class="section-title text-white">
            3 étapes pour<br /><span class="text-gradient">commencer.</span>
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div v-for="(step, i) in steps" :key="step.title" class="relative">
            <div class="bg-dark-800 border border-dark-600 p-8" style="clip-path: polygon(0 0, calc(100% - 12px) 0, 100% 12px, 100% 100%, 12px 100%, 0 calc(100% - 12px))">
              <span class="font-display font-black text-6xl text-dark-600 leading-none block mb-4">0{{ i + 1 }}</span>
              <h3 class="font-display font-black text-white uppercase text-xl mb-3">{{ step.title }}</h3>
              <p class="text-zinc-500 text-sm leading-relaxed">{{ step.desc }}</p>
            </div>
            <!-- Connector -->
            <div v-if="i < 2" class="hidden md:block absolute top-1/2 -right-3 z-10">
              <div class="w-6 h-0.5 bg-orange-500"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA FINAL -->
    <section class="py-24 px-4 bg-dark-900 relative overflow-hidden">
      <div class="absolute inset-0 z-0">
        <img src="/images/cardio.jpg" alt="Salle d'entraînement" class="w-full h-full object-cover opacity-20" />
      </div>
      <div class="relative z-10 max-w-3xl mx-auto text-center">
        <p class="label-tag text-orange-500 mb-6">Prêt à changer ?</p>
        <h2 class="section-title text-white mb-6">
          Votre transformation<br /><span class="text-gradient">commence aujourd'hui.</span>
        </h2>
        <p class="text-zinc-400 mb-10 text-lg">
          Créez votre compte gratuitement et réservez votre première séance avec Marc en quelques secondes.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link to="/register" class="btn-primary text-sm py-4 px-10 text-center">
            Créer mon compte
          </router-link>
          <router-link to="/seances" class="btn-ghost text-sm py-4 px-10 text-center">
            Voir le planning
          </router-link>
        </div>
      </div>
    </section>

  </div>
</template>

<script setup>
const stats = [
  { value: '10', label: 'Ans d\'expérience' },
  { value: '200+', label: 'Clients coachés' },
  { value: '5', label: 'Séances / semaine' },
  { value: '48h', label: 'Annulation gratuite' },
]

const certifications = [
  'BPJEPS Activités Physiques',
  'CrossFit Level 2 Trainer',
  'Certification Nutrition Sportive',
  'Préparateur Physique Certifié',
  'Premiers Secours (PSC1)',
  'Coach Remise en Forme',
]

const points = [
  {
    title: 'Réservez un créneau',
    desc: 'Choisissez une date et une heure qui vous conviennent dans le planning de Marc.',
  },
  {
    title: 'Présentez-vous',
    desc: 'Venez à la séance. Pas de préparation requise — juste vous, prêt à travailler.',
  },
  {
    title: 'Marc adapte tout',
    desc: 'En fonction de votre forme, vos envies et vos objectifs du jour, Marc construit la séance avec vous sur place.',
  },
]

const steps = [
  {
    title: 'Créez votre compte',
    desc: 'Inscription gratuite en moins d\'une minute. Juste un email et un mot de passe.',
  },
  {
    title: 'Choisissez un créneau',
    desc: 'Consultez le planning et réservez la date qui vous convient en un clic.',
  },
  {
    title: 'Venez vous entraîner',
    desc: 'Présentez-vous à l\'heure. Marc s\'occupe du reste — programme, matériel, coaching.',
  },
]
</script>
