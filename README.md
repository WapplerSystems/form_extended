# form_extended

## What does it do?

It extends the form extension with the following features:

- country select box by using static_info_tables
- CopyToSenderEmailFinisher:
- Multiple file upload support
  ![Multiple file upload!](Documentation/Images/multiple_upload.png "Multiple file upload")
- Privacy policy checkbox: Set ID of policy page
- New property fields:
  - info
- Choose template for email
- Choose language for email
- Sender registration in site sets: Editors can choose valid senders in the plugin settings
- New view helpers:
  - RenderProcessedFormValueViewHelper: Render value of a single form field

## Feature: Set senders in site configuration

With the sender-in-site feature, administrators can define valid senders in the site configuration and editors can select them in the plugins.

First step: Activate the feature in the side wide options.

![Activation!](Documentation/Images/Sender/activation.png "Activation")

Second step: Configure new valid addresses

![Sites configuration!](Documentation/Images/Sender/sites.png "Sites configuration")

It is no longer possible to set the sender manually.

![Finisher settings!](Documentation/Images/Sender/finisher.png "Finisher settings")

Third step: Choose sender address in plugin

![Plugin settings!](Documentation/Images/Sender/plugin.png "Plugin settings")


