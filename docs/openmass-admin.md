Administrative Notes for OpenMass project

Fetching DB/files from the Mass project
-------------
- drush sql-sync --sanitize @massgov.prod @self
- drush config-delete encrypt.profile.two_factor_auth
- drush pm-uninstall ga_login,tfa,encrypt,password_policy_characters,password_policy_length,password_policy,restrict_by_ip,acquia_connector,crazyegg,google_tag,security_review,seckit,datalayer,username_enumeration_prevention,flood_unblock
- remove google maps client api key
  - drush cset google_map_field.settings google_map_field_apikey YOURKEYHERE
  - drush cset google_map_field.settings google_map_field_map_client_id YOURIDHERE
- drush sql-dump --result=file=dump.sql --gzip
- drush rsync @massgov.prod:%files @self:%files && make-a-tarball
- delete any dirs containing derivative files (css/js/styes)
- clean up the symlinks
- consider removing silly files: .DS_Store, etc.
- tar -cvzf files.tar.gz .

Fetching new code from Mass project
-------------
- Merge PR from develop to openmass_master branches.
  - Resolve any conflicts
  - Make sure tests pass
- Push openmass_master branch to openmass repo

Making a new OpenMass release
----------
- Make sure tests are passing on master branch
- Release notes may be built from the commits on massgov/develop 
- Create a new release via Github releases
- Push both tarballs to a Github release or [S3](https://console.aws.amazon.com/s3/buckets/openmass/?region=us-east-1&tab=overview)

Differences from Mass.Gov
---------
- Mass.Gov incorporates a number of security enhancements.  This project omits the configuration of those enhancements on the theory that any organization should implement its own policies.   