{{ content() }}

<form method="post" autocomplete="off">

    <style type='text/css'>
        .table th.leftCell,
        .table td.leftCell {
            text-align: left;
        }
        .table th.rightCell,
        .table td.rightCell {
            text-align: right;
        }
    </style>
<div class="container">
    <h2>Create Meta Tag Template</h2>
    
    <table class='table table-borderless'>
        <tbody>
            <tr>
                <td class='rightCell'><label for="meta_name">Name</label></td>
                <td class='leftCell'>{{ form.render("meta_name") }}</td>
            </tr>
            <tr>
                <td class='rightCell'><label for="template">Template</label></td>
                <td class='leftCell'>{{ form.render("template") }}</td>
            </tr>
            <tr>
                <td class='rightCell'><label for="data_limit">Data Size</label></td>
                <td class='leftCell'>{{ form.render("data_limit") }}</td>
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


