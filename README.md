# Sortables Captcha

Sortables Captcha Extension for phpBB. This captcha plugin allows you to add questions and provide answers in two separate columns. An user will see all the answers in one column and has to drag some answers to the other column to successfully complete the captcha.

[![Build Status](https://github.com/Derky/Sortables-CAPTCHA-Plugin/workflows/Tests/badge.svg)](https://github.com/Derky/Sortables-CAPTCHA-Plugin/actions)

## Install development version
1. Create the following folder structure in your phpBB's `ext` directory: `derky/sortablescaptcha`.
2. Download the latest development version from the master branch and extract it into the `derky/sortablescaptcha` folder.
3. Follow **step 3 to 5** from the production installation instructions below. 


## Install production version
1. [Download the latest validated release from phpBB.com](https://www.phpbb.com/customise/db/extension/sortables_captcha/).
2. Extract the ZIP file and copy the `derky` folder into your phpBB's `ext` directory.
3. In your **Admin Control Panel (ACP)**, go to **Customise -> Manage extensions** and enable **Sortables Captcha**.
4. In the **Admin Control Panel (ACP)**, go to **General -> Spambot countermeasures**, select **Sortables Captcha**, click **Configure** to add questions and answers.
5. Set **Sortables** as the default captcha in **General -> Spambot countermeasures**.


## Update

1. Go to the **Admin Control Panel** -> **Customise** -> **Manage extensions**
2. Disable the extension (**do _not_** click "delete data" to keep all your configured questions and answers)
3. Delete folder `ext/derky/sortablescaptcha`
4. Upload the new version
5. Enable the extension (this will run the necessary migrations and event updates)

**If you replaced the files before disabling the extension**

You'll probably get an HTTP 500 error (blank page). Restore the files of the previous version and follow the recommended update guide above. The previous version can be downloaded from the "Revisions" tab on the [Extension Page](https://www.phpbb.com/customise/db/extension/sortables_captcha/).

## Translations
Translations are included with the extension. Current translations can be found [in the language folder](https://github.com/Derky/Sortables-CAPTCHA-Plugin/tree/master/language).
We also welcome new translations! You can contribute by:

- [Pull Request via GitHub](https://github.com/Derky/Sortables-CAPTCHA-Plugin/tree/master/language)
- [Private Message on phpBB.com](http://www.phpbb.com/community/ucp.php?i=pm&mode=compose&u=178494)

## License
[GNU General Public License v2](https://opensource.org/licenses/GPL-2.0)
