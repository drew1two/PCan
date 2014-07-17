{{ content() }}

<ul class="pager">
    <li class="previous pull-left">
        {{ link_to("profiles/index", "&larr; Go Back") }}
    </li>
    <li class="pull-right">
        {{ link_to("profiles/new", "New Profile", "class": "btn btn-primary") }}
    </li>
</ul>
<div class="btn-group">
    {{ link_to("profiles/index", '<i class="icon-fast-backward"></i> First', "class": "btn") }}
    {{ link_to("profiles/index?page=" ~ page.before, '<i class="icon-step-backward"></i> Previous', "class": "btn ") }}
    {{ link_to("profiles/index?page=" ~ page.next, '<i class="icon-step-forward"></i> Next', "class": "btn") }}
    {{ link_to("profiles/index?page=" ~ page.last, '<i class="icon-fast-forward"></i> Last', "class": "btn") }}
</div>
<span class="help-inline">{{ page.current }}/{{ page.last }}</span>

<table class="table table-bordered table-striped" align="center">
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Active</th>
        </tr>
    </thead>
    {% for profile in page.items %}
    <tbody>
        <tr>
            <td>{{ profile.id }}</td>
            <td>{{ profile.name }}</td>
            <td>{{ profile.active == 'Y' ? 'Yes' : 'No' }}</td>
            <td width="12%">{{ link_to("profiles/edit/" ~ profile.id, '<i class="icon-pencil"></i> Edit', "class": "btn") }}</td>
            <td width="12%">{{ link_to("profiles/delete/" ~ profile.id, '<i class="icon-remove"></i> Delete', "class": "btn") }}</td>
        </tr>
    </tbody>
    {% endfor %}

</table>


