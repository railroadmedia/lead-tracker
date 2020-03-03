# Lead Tracker
Tracks email form submissions and marketing data for visitor origins.

# How It Works

1. A potential customer clicks a link in one of our marketing emails or an ad which sends them to a lead gen
page on one of our sites. The URL will generally have tracking info in the parameters. Examples: 

```text
http://www.drumeo.com/faster?utm_source=facebook&utm_medium=cpc&utm_campaign=ZFWFS&utm_term=Z1_01&utm_content=story1
https://www.pianote.com/shop?utm_source=maropost&utm_medium=email&utm_campaign=digital&utm_term=pianists&utm_content=shop
```

2. When the customer enters their email and submits the form, they should be added to maropost and tagged accordingly.
A new row in the tracking table should also be created with the data that was in the URL. Table columns:

```text
id
email
maropost_tag_name
utm_source (can be empty)
utm_medium (can be empty)
utm_campaign (can be empty)
utm_term (can be empty)
```

3. In order to do this the form needs to read from the URL and add that data as input info for the form.

4. We will use middleware to capture the normal requests which only create and tag a maropost contact. The forms POST
to here so we'll capture that request by configuration and create the row first using this package:

```text
/maropost/form/sync-contact
```