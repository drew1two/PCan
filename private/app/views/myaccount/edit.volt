  {{ content() }}
<form method="post" autocomplete="off">
  
    <div class="center scaffold">
        <table class='table table-borderless table-condensed'>
            <tr><td><h2>My Account</h2></td>
                <td>{{ submit_button("Save", "class": "btn btn-big btn-success") }}</td>
            </tr>
        </table>
        

        <ul class="nav nav-tabs">
            <li class="active"><a href="#A" data-toggle="tab">Basic</a></li>
            <li><a href="#B" data-toggle="tab">Successful Logins</a></li>
            <li><a href="#C" data-toggle="tab">Password Changes</a></li>
            <li><a href="#D" data-toggle="tab">Reset Passwords</a></li>
        </ul>

        <div class="tabbable">
            <div class="tab-content">
                <div class="tab-pane active container" id="A">
                        {{ form.render("id",{'class':'span8 form-inline'}) }}
                    <table class="table table-striped">
                        <tr>
                            <td><label for="name">Name</label></td>
                            <td>{{ form.render("name") }}</td>
                        </tr>
                        <tr>
                            <td><label for="email">E-Mail</label></td>
                            <td>{{ form.render("email") }}</td>
                        </tr>
                        <tr>
                            <td><label for="profilesId">Profile</label></td>
                            <td>{{ form.render("profilesId") }}</td>
                        </tr>
                   </table>
                    <table class="table  table-striped" style='width:250px;'>
                         <tr>
                            <td><label for="suspended">Suspended?</label></td>
                            <td>{{ form.render("suspended") }}</td>
                        </tr>                        
                        <tr>
                            <td><label for="banned">Banned?</label></td>
                            <td>{{ form.render("banned") }}</td>
                        </tr>                        
                        <tr>
                            <td><label for="active">Confirmed?</label></td>
                            <td>{{ form.render("active") }}</td>
                        </tr>
                    </table>   


                </div>

                <div class="tab-pane" id="B">
                    <p>
                    <table class="table table-bordered table-striped" align="center">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>IP Address</th>
                                <th>User Agent</th>
                            </tr>
                        </thead>
                        <tbody>
                            {% for login in user.successLogins %}
                            <tr>
                                <td>{{ login.id }}</td>
                                <td>{{ login.ipAddress }}</td>
                                <td>{{ login.userAgent }}</td>
                            </tr>
                            {% else %}
                            <tr><td colspan="3" align="center">User does not have successfull logins</td></tr>
                            {% endfor %}
                        </tbody>
                    </table>
                    </p>
                </div>

                <div class="tab-pane" id="C">
                    <div class='container'>
                        <div class='row'>
                        <br/>
                        {{ link_to('users/changePassword',"Change Password",'class':'btn btn-primary') }}
                        </div>
                        <br/>
                    </div>
                    <table class="table table-bordered table-striped" align="center">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>IP Address</th>
                                <th>User Agent</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            {% for change in user.passwordChanges %}
                            <tr>
                                <td>{{ change.id }}</td>
                                <td>{{ change.ipAddress }}</td>
                                <td>{{ change.userAgent }}</td>
                                <td>{{ date("Y-m-d H:i:s", change.createdAt) }}</td>
                            </tr>
                            {% else %}
                            <tr><td colspan="3" align="center">User has not changed his/her password</td></tr>
                            {% endfor %}
                        </tbody>
                    </table>
                    </p>
                </div>

                <div class="tab-pane" id="D">
                    <p>
                    <table class="table table-bordered table-striped" align="center">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Date</th>
                                <th>Reset?</th>
                            </tr>
                        </thead>
                        <tbody>
                            {% for reset in user.resetPasswords %}
                            <tr>
                                <th>{{ reset.id }}</th>
                                <th>{{ date("Y-m-d H:i:s", reset.createdAt) }}
                                <th>{{ reset.reset == 'Y' ? 'Yes' : 'No' }}
                            </tr>
                            {% else %}
                            <tr><td colspan="3" align="center">User has not requested reset his/her password</td></tr>
                            {% endfor %}
                        </tbody>
                    </table>
                    </p>
                </div>

            </div>
        </div>

</form>
</div>