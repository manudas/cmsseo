<h1>{l s='Welcome the backup / restore Combination Seo Module page!' d='Modules.combinationseo'}</h1>

<form method='post' id='combinationSeoBackup' >
    <input type='hidden' name='submitBackup' />
    <br />
    <legend>{l s='Backup your data' d='Modules.combinationseo'}</legend>
    <table id='tablebackup'>
      <tr>
        <td>
          <label for='extracts'>{l s='Your code extracts in XML' d='Modules.combinationseo'}</label>
        </td>
        <td>  
          <input type='submit' name='extracts' id='extracts' value='{l s='Data extracts' d='Modules.combinationseo'}'>
        </td>
      </tr>
      <tr>
        <td>
          <label for='metadata'>{l s='The meta data for your pages (CSV or XML, think about)' d='Modules.combinationseo'}</label>
        </td>
        <td>
          <input type='submit' name='metadata' id='metadata' value='{l s='Meta data' d='Modules.combinationseo'}'><br>
        </td>
      </tr>
      <tr>
        <td>
          <label for='combinations'>{l s='The possible combinations of your extracts (CSV or XML, think about)' d='Modules.combinationseo'}</label>
        </td>
        <td>
          <input type='submit' name='combinations' id='combinations' value='{l s='Combinations' d='Modules.combinationseo'}'><br>
        </td>
      </tr>
      <tr>
        <td>
          <label for='replacements'>{l s='The possible keywords replacements of your extracts (CSV or XML, think about)' d='Modules.combinationseo'}</label>
        </td>
        <td>
          <input type='submit' name='replacements' id='replacements' value='{l s='Replacements' d='Modules.combinationseo'}'><br>
        </td>
      </tr>
    </table>
  </fieldset>
</form>

<form method='post' id='combinationSeoRestore' >
    <input type='hidden' name='submitRestore' />
    <br />
    <legend>{l s='Restore or upload your data' d='Modules.combinationseo'}</legend>
    <table id='tablerestore'>
      <tr>
        <td>
          <label for='fileselector'>{l s='Select the file to restore' d='Modules.combinationseo'}</label>
        </td>
        <td>  
          <input id="uploadFile" placeholder="{l s='Choose File' d='Modules.combinationseo'}" disabled="disabled" />
          <div class="fileUpload btn btn-primary">
              <span>{l s='Select file' d='Modules.combinationseo'}</span>
              <input id="combination_file" type="file" class="upload" name='combination_file'/>
          </div>
        </td>
      </tr>
      <tr>
        <td>
          <label for='extracts'>{l s='Your code extracts in XML' d='Modules.combinationseo'}</label>
        </td>
        <td>  
          <input type='submit' name='extracts' id='extracts' value='{l s='Data extracts' d='Modules.combinationseo'}'>
        </td>
      </tr>
      <tr>
        <td>
          <label for='metadata'>{l s='The meta data for your pages (CSV or XML, think about)' d='Modules.combinationseo'}</label>
        </td>
        <td>
          <input type='submit' name='metadata' id='metadata' value='{l s='Meta data' d='Modules.combinationseo'}'><br>
        </td>
      </tr>
      <tr>
        <td>
          <label for='combinations'>{l s='The possible combinations of your extracts (CSV or XML, think about)' d='Modules.combinationseo'}</label>
        </td>
        <td>
          <input type='submit' name='combinations' id='combinations' value='{l s='Combinations' d='Modules.combinationseo'}'><br>
        </td>
      </tr>
      <tr>
        <td>
          <label for='replacements'>{l s='The possible keywords replacements of your extracts (CSV or XML, think about)' d='Modules.combinationseo'}</label>
        </td>
        <td>
          <input type='submit' name='replacements' id='replacements' value='{l s='Replacements' d='Modules.combinationseo'}'><br>
        </td>
      </tr>
    </table>
  </fieldset>
</form>