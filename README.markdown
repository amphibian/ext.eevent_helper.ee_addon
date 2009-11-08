**IMPORTANT NOTE: if you're upgrading from version 1.0.4 or lower, please *deactivate* EEvent Helper before upgrading, as your old settings will not be compatible with version 1.1's configuration.**

EEvent Helper is an ExpressionEngine extension that makes managing an events weblogs much more intuitive.

Once activiated, when entries are published in any of your specified "events" weblogs, their *Expiration Date* will be automatically set to 23:59:59 on the day of the event. Or, if you choose a custom date field to serve as an *End Date* indicator, their *Expiration Date* will be automatically set to 23:59:59 on the *End Date* (if it's not empty).

You can also specify a custom *Start Date* field to use instead of the *Entry Date* calendar from the **Date** tab. Once you specify this field, you can automatically set the *Entry Date* to match the custom *Start Date* field, eliminating the need for the **Date** tab altogether.

This way, you and your clients can use friendlier custom date fields for both start and end dates, while always keeping the entry's *Entry Date* and *Expiration Date* set properly for use in `exp:weblog:entries` tag parameters.

Optionally, you can remove the "time" portion of date fields in your chosen events weblogs (with time set to 00:00:00 upon publishing); and also remove the date localization toggle your events weblogs' date fields.

Note that only fields of the built-in ExpressionEngine "Date" fieldtype can be selected as *Start Date* or *End Date* fields.

*EEvent Helper has been tested on ExpressionEngine 1.6.8, and requires CP jQuery loading jQuery 1.3+*.