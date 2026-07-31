/* ============================================
   NUFA GLOBAL EDUCATION — Site Config
   Edit the values below to swap media without touching any HTML.
   Each field accepts either a local path (e.g. "assets/video/hero-loop.mp4")
   or a full URL to a video hosted elsewhere (e.g. a CDN / direct .mp4 link).
   After editing, just re-upload this one file to Rumahweb — no other
   changes needed.
   ============================================ */
window.SITE_CONFIG = {
  // Full-bleed cinematic background video on the homepage hero.
  // Uses a root-absolute path (leading /) so it resolves correctly from
  // both the Indonesian pages at the site root and the English pages
  // under /en/ that share this same config file.
  heroVideoUrl: '/assets/video/hero-loop.mp4',
  // Still image shown while the hero video is loading.
  // Left blank because assets/hero-poster.jpg was never uploaded — pointing
  // at a missing file makes every homepage load fire a 404. Fill this back
  // in once the poster JPG (see assets/README-ASSETS.md) is in place.
  heroVideoPoster: '',

  // Video opened by the "Tonton Video Profil" / "Watch Profile Video" button in the hero.
  companyProfileVideoUrl: '/assets/video/company-profile.mp4',
};
