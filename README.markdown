EEvent Helper is an ExpressionEngine extension that makes managing an events-type weblog much more intuitive.

Once activiated, when entries are published in your specified "events" weblog, their *Expiration Date* will be automatically set to 23:59:59 on the day of the event. Or, if you choose a custom date field to serve as an *End Date* indicator, their *Expiration Date* will be automatically set to 23:59:59 on the *End Date* (if it's not empty).

You can also specify a custom *Start Date* field to use instead of the *Entry Date* calendar from the **Date** tab. Once you specify this field, you can automatically set the *Entry Date* to match the custom *Start Date field*, eliminating the need for the **Date** tab altogether.  This way, you and your clients can use friendlier custom date fields for both start and end dates, while always keeping the entry's *Entry Date* and *Expiration Date* set properly for use in `exp:weblog:entries` tag parameters.

Lastly, you can force the *Start Date* and *End Date* fields to have their time set to 00:00:00 upon publishing.

Note that only fields of the built-in ExpressionEngine "Date" fieldtype can be selected as *Start Date* or *End Date* fields.

EEvent Helper has been tested on ExpressionEngine 1.6.8.