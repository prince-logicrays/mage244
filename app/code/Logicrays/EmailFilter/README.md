# About  Extension
This extension allows you to prevent unauthorised access to certain domains or email addresses on your store.

# Related GraphQL
* type StoreConfig

    type StoreConfig {
        email_filter_enable : String @doc(description: "To fetch value of module enable or disable"),
        email_filter_registrationrestrict : String @doc(description: "filter on registration page"),
        email_filter_checkoutrestrict : String @doc(description: "filter on checkout page"),
        email_filter_contactrestrict : String @doc(description: "filter on contact page"),
        email_filter_newslatterrestrict : String @doc(description: "filter on newslatter page"),
        email_filter_emaildomainrestrict : String @doc(description: "get value of email domain restrict")
    }

-> This graphql returns all of the admin-side configuration values.

**Please Note.**

1. **email_filter_emaildomainrestrict**: Whatever domain or particular email address is available in this field, you have to apply 
    it based on the below conditions.
    For Example: Suppose you will receive the gmail.com domain in this field, and at your frontend side you will receive 
    the test@gmail.com email in your applicable page, then you have to throw an error like [This email id is not applicable
    for this store].

2. **email_filter_enable**: In this field, you get an output of 1, and only then does this extension function in your store.

3. **email_filter_registrationrestrict**: In this field, you get an output of 1, and email_filter_enable in this field of
    output is also 1, so you can only restrict the email filter on the registration page.

4. **email_filter_checkoutrestrict**: In this field, you get an output of 1, and email_filter_enable in this field of output
    is also 1, so you can only restrict the email filter on the checkout page.

5. **email_filter_contactrestrict**: In this field, you get an output of 1, and email_filter_enable in this field of output is
    also 1, so you can only restrict the email filter on the contact us page.

6. **email_filter_newslatterrestrict**: In this field, you get an output of 1, and email_filter_enable in this field of output is
    also 1, so you can only restrict the email filter on the Newslater page.
