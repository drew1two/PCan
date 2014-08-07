{{ content() }}

<form method="post" autocomplete="off">


<div class="container">
    <h2>Create Meta Tag Template</h2>
    
    <table class='table table-borderless'>
        <tbody>
            <tr>
                <td class='rightCell'><label for="attr_value">Attribute Value</label></td>
                <td class='leftCell'>{{ form.render("attr_value") }}</td>
            </tr>
            <tr>
                <td class='rightCell'><label for="attr_name">Attribute Name</label></td>
                <td class='leftCell'>{{ form.render("attr_name") }}</td>
            </tr>
            <tr>
                <td class='rightCell'><label for="content_type">Content Type</label></td>
                <td class='leftCell'>{{ form.render("content_type") }}</td>
            </tr>
            <tr>
                <td class='rightCell'><label for="auto_filled">Auto Filled</label></td>
                <td class='leftCell'>{{ form.render("auto_filled") }}</td>
            </tr>
            <tr>
                <td colspan='2'>{{ submit_button("Save", "class": "btn btn-success") }}</td>
            </tr>
        </tbody>
    </table>
</div>
</form>


