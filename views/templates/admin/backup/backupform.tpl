<h1>{l s='Welcome the backup / restore Combination Seo Module page!' d='Modules.combinationseo'}</h1>

<form method="post">
    <br />
    <legend>{l s='Backup your data' d='Modules.combinationseo'}</legend>
    <table id="tablebackup">
      <tr>
        <td>
          <label for="extracts">{l s='Your code extracts in XML' d='Modules.combinationseo'}</label>
        </td>
        <td>  
          <input type="button" name="gender" id="extracts" value="{l s='Data extracts' d='Modules.combinationseo'}">
        </td>
      </tr>
      <tr>
        <td>
          <label for="metadata">{l s='The meta data for your pages (CSV or XML, think about)' d='Modules.combinationseo'}</label>
        </td>
        <td>
          <input type="button" name="metadata" id="metadata" value="{l s='Meta data' d='Modules.combinationseo'}"><br>
        </td>
      </tr>
      <tr>
        <td>
          <label for="combinations">{l s='The possible combinations of your extracts (CSV or XML, think about)' d='Modules.combinationseo'}</label>
        </td>
        <td>
          <input type="button" name="combinations" id="combinations" value="{l s='Combinations' d='Modules.combinationseo'}"><br>
        </td>
      </tr>
      <tr>
        <td>
          <label for="replacements">{l s='The possible keywords replacements of your extracts (CSV or XML, think about)' d='Modules.combinationseo'}</label>
        </td>
        <td>
          <input type="button" name="replacements" id="replacements" value="{l s='Replacements' d='Modules.combinationseo'}"><br>
        </td>
      </tr>
      <tr>
        <td colspan=2>
          <input type="hidden" name="action" id="action" />
          <input type="submit" value="Submit">
        </td>
      </tr>
    </table>
  </fieldset>
</form>