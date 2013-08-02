<methodResponse>
    <fault>
        <value>
            <struct>
                <member>
                    <name>faultCode</name>
                    <value><int><?=$vars['faultCode'];?></int></value>
                </member>
                
                <member>
                    <name>faultString</name>
                    <value><string><?=$vars['message'];?></string></value>
                </member>
            </struct>
        </value>
    </fault>
</methodResponse>