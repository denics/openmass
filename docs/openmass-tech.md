Short Term
============
- Need Github repo. It will be hard to market this successfully with a private repo. What would be the process to add outsiders?
- Sanitized sql dump (sanitizer shared back to Massgov codebase)
- Tarball for files dir.
- Code_composer for codebase assembly
- Modifications from massgov
  - disable TFA, password_policy, restrict_by_ip, acquia_connector,crazyEgg,GoogleTagManager,SecurityReview,SecurityKit,dataLayer
  -  Do these modifications as post-install Drush command
- Write Install instructions
- Setup demo site, with regular wipe+reinstall. Hosted where?

Long Term
============= 
- No SQL dump
- No post-install instructions. Modifications have already been made in composer.json, composer.lock, config-sync
- Install starter content via default_content module. This could potentially be useful as test content for massgov team.

Open
=========
- Possible issues with palantirnet/thebuild or other private repos
- How to highlight and incorporate Mayflower. FYI only mayflower-artifacts is public.
- Give guidance on making a traditional custom Drupal theme for this platform.
- Security
  - restricted IP list in settings
  - Basic Auth password in settings
  - TFA backup codes in DB need sanitizing
  
