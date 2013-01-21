// Configuration support javascript

function diconfig_add_row()
{
  var html = new HTMLex;
  var row_info = document.getElementById('row_info');
  // Create a new row.
  // Now populate it with the row information.
  var field_count = document.getElementById('field_count');
  var row_id = 0;
  if (field_count)
    row_id = new Number(field_count.value);
  row_id++;
  var new_row = html.addRow('field_row^' + row_id);
  // First field is the from fieldname.
  new_row.appendChild(html.addCellNode(diconfig_add_input('dimap_source_field', row_id)));
  new_row.appendChild(html.addCellNode(diconfig_add_select(diconfig_target_tables, 'dimap_target_table', row_id, diconfig_set_target)));
  new_row.appendChild(html.addCellNode(diconfig_add_select(null, 'dimap_target_field', row_id)));
  new_row.appendChild(html.addCellNode(diconfig_add_checkbox('del_field', row_id)));
  if (field_count)
    field_count.value = row_id;
  else
    document.changeformat.appendChild(html.addHidden('field_count',row_id)); 
  row_info.appendChild(new_row);
}

function diconfig_del_row(id)
{
  // Check to see if any checkboxes are set and if so, delete the
  // associated row.
  var tbl = document.getElementById('row_info');
  var field_count = document.getElementById('field_count');
  if (field_count) {
    var count = new Number(field_count.value);
    for (var i = 1; i <= count; i++) {
      var row = document.getElementById('del_field^' + i);
      if (row && row.checked) {
	tbl.removeChild(document.getElementById('field_row^' + i));
      }
    }
  }
  return true;
}

function diconfig_add_select(source, name, id, handler)
{
  var html = new HTMLex;
  var c = new Comparable;
  if (id) {
    c.add('id', name + '^' + id);
    c.add('name', name + '[' + id + ']');
  } else {
    c.add('id', name);
    c.add('name', name);
  }
  c.add('class', 'text');
  var sel = html.addNode('SELECT', false, c);
  if (source) {
    // Add an initial option that does nothing
    sel.appendChild(html.addOption('', sel_from_list));
    var len = source.length();
    for (var i = 0; i < len; i++) {
      var elem = source.get(i);
      sel.appendChild(html.addOption(elem.key, elem.key));
    }
  }
  if (handler)
    sel.onchange = handler;
  return sel;
}

function diconfig_set_target(ev)
{
  var html = new HTMLex;
  var e = new CommonEvent(ev);
  // grab the id, to get the target field
  var nameparts = e.target.id.split('^');
  var num = nameparts[1];
  // Determine what the current value is and set the appropriate field
  // First clear out the target
  var target_field = clear_span('dimap_target_field^' + num);
  if (e.target.selectedIndex >= 0) {
    var val = e.target.options[e.target.selectedIndex].value;
    var fieldlist = diconfig_target_tables.find(val);
    if (fieldlist) {
      for (var i = 0; i < fieldlist.length; i++) {
	target_field.appendChild(html.addOption(fieldlist[i], fieldlist[i], i == 0));
      }
    }
  } 
  return true;
}

function diconfig_add_input(name, id, value, size, maxlength)
{
  var html = new HTMLex;
  var c = new Comparable;
  if (id) {
    c.add('name', name + '[' + id + ']');
    c.add('id', name + '^' + id);
  } else {
    c.add('name', name);
    c.add('id', name);
  }
  if (size)
    c.add('size', size);
  if (maxlength)
    c.add('maxlength', maxlength);
  if (value)
    c.add('value', value);
  c.add('class', 'text');
  return html.addNode('INPUT', false, c);
}

function diconfig_add_checkbox(name, id)
{
  var html = new HTMLex;
  var c = new Comparable;
  if (id) {
    c.add('name', name + '[' + id + ']');
    c.add('id', name + '^' + id);
  } else {
    c.add('name', name);
    c.add('id', id);
  }
  c.add('type', 'checkbox');
  c.add('class', 'button');
  return html.addNode('INPUT', false, c);
}

