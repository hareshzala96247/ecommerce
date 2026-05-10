import { reactive } from 'vue';

export const settings = reactive({
  site_name:        '',
  site_tagline:     '',
  logo:             null,
  favicon:          null,
  contact_email:    '',
  contact_phone:    '',
  contact_address:  '',
  social_facebook:  '',
  social_instagram: '',
  social_twitter:   '',
  social_youtube:   '',
  meta_title:       '',
  meta_description: '',
});

export async function loadSettings() {
  try {
    const res  = await fetch('/api/settings');
    const data = await res.json();
    Object.assign(settings, data);
  } catch {}
}
